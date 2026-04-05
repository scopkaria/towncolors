<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\UserAlertNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_unless(in_array($user->role, [UserRole::ADMIN, UserRole::CLIENT], true), 403);

        $query = Invoice::with(['project.client', 'project.freelancer'])
            ->latest();

        // Role-based scoping
        if ($user->role === UserRole::CLIENT) {
            $query->whereHas('project', fn ($q) => $q->where('client_id', $user->id));
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('currency')) {
            $query->where('currency', $request->input('currency'));
        }
        if ($request->filled('client') && $user->role === UserRole::ADMIN) {
            $query->whereHas('project', fn ($q) => $q->where('client_id', $request->input('client')));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $invoices = $query->get();

        // Admin needs client list for filter dropdown
        $clients = $user->role === UserRole::ADMIN
            ? User::where('role', UserRole::CLIENT)->orderBy('name')->get(['id', 'name'])
            : collect();

        $filters = $request->only(['status', 'currency', 'client', 'date_from', 'date_to']);

        $viewMap = [
            'admin' => 'admin.invoices.index',
            'client' => 'client.invoices.index',
        ];

        return view($viewMap[$user->role->value], compact('invoices', 'clients', 'filters'));
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        $projects = Project::with('client')
            ->doesntHave('invoice')
            ->latest()
            ->get();

        return view('admin.invoices.create', compact('projects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id', 'unique:invoices,project_id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'currency' => ['required', 'in:TZS,USD'],
        ]);

        $currency = $validated['currency'];
        $amount = (float) $validated['amount'];
        $exchangeRate = null;
        $convertedAmount = null;

        if ($currency === 'USD') {
            $exchangeRate = $this->getExchangeRate();
            $convertedAmount = round($amount * $exchangeRate, 2);
        } elseif ($currency === 'TZS') {
            $exchangeRate = $this->getExchangeRate();
            $convertedAmount = $exchangeRate > 0 ? round($amount / $exchangeRate, 2) : null;
        }

        $invoice = Invoice::create([
            'project_id' => $validated['project_id'],
            'total_amount' => $amount,
            'paid_amount' => 0,
            'currency' => $currency,
            'exchange_rate' => $exchangeRate,
            'converted_amount' => $convertedAmount,
            'status' => 'unpaid',
        ]);

        $invoice->load('project.client', 'project.freelancer');
        $this->notifyInvoiceStakeholders(
            $invoice,
            'Invoice created',
            'A new invoice was created for project "' . $invoice->project->title . '".',
            'New invoice created'
        );

        return redirect()->route('admin.invoices')
            ->with('success', 'Invoice created successfully.');
    }

    public function show(Request $request, Invoice $invoice): View
    {
        $user = $request->user();
        $invoice->load(['project.client', 'project.freelancer', 'payments']);

        abort_unless(in_array($user->role, [UserRole::ADMIN, UserRole::CLIENT], true), 403);

        if ($user->role === UserRole::CLIENT) {
            abort_unless($invoice->project->client_id === $user->id, 403);
        }

        return view('invoices.show', [
            'invoice' => $invoice,
            'role' => $user->role->value,
        ]);
    }

    public function edit(Request $request, Invoice $invoice): View
    {
        $this->authorizeAdmin($request);
        $invoice->load('project.client', 'payments');

        return view('admin.invoices.edit', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $paidAmount = (float) $invoice->paid_amount;

        $validated = $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:' . number_format(max(0.01, $paidAmount), 2, '.', ''),
                'max:99999999.99',
            ],
            'currency' => ['required', 'in:TZS,USD'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $amount = (float) $validated['amount'];
        $currency = $validated['currency'];

        $exchangeRate = $this->getExchangeRate();
        $convertedAmount = null;

        if ($currency === 'USD') {
            $convertedAmount = round($amount * $exchangeRate, 2);
        } elseif ($currency === 'TZS') {
            $convertedAmount = $exchangeRate > 0 ? round($amount / $exchangeRate, 2) : null;
        }

        // Recalculate paid_amount in new currency if currency changed
        // paid_amount stays in its original numeric value — we keep amounts in their native currency
        // Recalculate status based on new total
        $remaining = $amount - $paidAmount;
        if ($remaining <= 0.001) {
            $status = 'paid';
        } elseif ($paidAmount > 0) {
            $status = 'partial';
        } else {
            $status = 'unpaid';
        }

        $invoice->update([
            'total_amount' => $amount,
            'currency' => $currency,
            'exchange_rate' => $exchangeRate,
            'converted_amount' => $convertedAmount,
            'expires_at' => $validated['expires_at'] ?: null,
            'status' => $status,
        ]);

        $invoice->load('project.client', 'project.freelancer');
        $this->notifyInvoiceStakeholders(
            $invoice,
            'Invoice updated',
            'Invoice INV-' . str_pad($invoice->id, 4, '0', STR_PAD_LEFT) . ' was updated for project "' . $invoice->project->title . '".',
            'Invoice updated'
        );

        return redirect()->route('admin.invoices')
            ->with('success', 'Invoice INV-' . str_pad($invoice->id, 4, '0', STR_PAD_LEFT) . ' updated successfully.');
    }

    public function addPayment(Request $request, Invoice $invoice): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $remaining = (float) $invoice->total_amount - (float) $invoice->paid_amount;

        $validated = $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:' . number_format($remaining, 2, '.', ''),
            ],
        ]);

        $paymentAmount = (float) $validated['amount'];

        Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $paymentAmount,
        ]);

        $newPaid = (float) $invoice->paid_amount + $paymentAmount;
        $newRemaining = (float) $invoice->total_amount - $newPaid;

        $invoice->update([
            'paid_amount' => $newPaid,
            'status' => $newRemaining <= 0.001 ? 'paid' : 'partial',
        ]);

        return back()->with('success', 'Payment of ' . number_format($paymentAmount, 2) . ' ' . $invoice->currency . ' recorded successfully.');
    }

    public function downloadPdf(Request $request, Invoice $invoice)
    {
        $user = $request->user();
        $invoice->load(['project.client', 'project.freelancer']);

        abort_unless(in_array($user->role, [UserRole::ADMIN, UserRole::CLIENT], true), 403);

        // Authorize: admin sees all; client only their own
        if ($user->role === UserRole::CLIENT) {
            abort_unless($invoice->project->client_id === $user->id, 403);
        }

        $settings = Setting::instance();

        $pdf = Pdf::loadView('pdf.invoice', compact('invoice', 'settings'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("invoice_{$invoice->id}.pdf");
    }

    /**
     * Fetch USD→TZS exchange rate with static fallback.
     */
    private function getExchangeRate(): float
    {
        try {
            $response = Http::timeout(5)
                ->get('https://open.er-api.com/v6/latest/USD');

            if ($response->successful()) {
                $rate = $response->json('rates.TZS');
                if ($rate && is_numeric($rate) && $rate > 0) {
                    return (float) $rate;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Exchange rate API unavailable, using fallback.', [
                'error' => $e->getMessage(),
            ]);
        }

        return Invoice::FALLBACK_RATE;
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()->role === UserRole::ADMIN, 403);
    }

    private function notifyInvoiceStakeholders(Invoice $invoice, string $title, string $message, string $subject): void
    {
        collect([$invoice->project->client])
            ->filter()
            ->unique('id')
            ->each(function ($recipient) use ($invoice, $title, $message, $subject) {
                $recipient->notify(new UserAlertNotification(
                    $title,
                    $message,
                    $subject,
                    route('invoices.show', $invoice),
                    'View Invoice',
                    projectId: $invoice->project_id,
                    invoiceId: $invoice->id,
                ));
            });
    }
}
