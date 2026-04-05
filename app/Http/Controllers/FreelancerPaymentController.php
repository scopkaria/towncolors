<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\FreelancerPayment;
use App\Models\FreelancerPaymentLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FreelancerPaymentController extends Controller
{
    /**
     * Admin: list all freelancer payments across all projects.
     */
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $query = FreelancerPayment::with(['project', 'freelancer', 'logs'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('freelancer')) {
            $query->where('freelancer_id', $request->input('freelancer'));
        }

        $payments  = $query->get();
        $freelancers = User::where('role', UserRole::FREELANCER)->orderBy('name')->get(['id', 'name']);
        $filters   = $request->only(['status', 'freelancer']);

        return view('admin.freelancer-payments.index', compact('payments', 'freelancers', 'filters'));
    }

    /**
     * Admin: show the payment panel for a specific project.
     */
    public function show(Request $request, Project $project): View
    {
        $this->authorizeAdmin($request);

        $project->load(['freelancer', 'freelancerPayment.logs']);
        $freelancerPayment = $project->freelancerPayment;

        return view('admin.freelancer-payments.show', compact('project', 'freelancerPayment'));
    }

    /**
     * Admin: set agreed amount for a project (creates record if none exists).
     */
    public function setAgreed(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeAdmin($request);

        abort_unless($project->freelancer_id !== null, 422, 'No freelancer assigned to this project.');

        $validated = $request->validate([
            'agreed_amount' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
        ]);

        $payment = $project->freelancerPayment;

        if ($payment) {
            // Prevent reducing agreed_amount below paid_amount
            if ((float) $validated['agreed_amount'] < (float) $payment->paid_amount) {
                return back()->withErrors([
                    'agreed_amount' => 'Agreed amount cannot be less than the already-paid amount (' . $payment->formattedPaid() . ').',
                ])->withInput();
            }

            $remaining = (float) $validated['agreed_amount'] - (float) $payment->paid_amount;
            $status = $remaining <= 0.001 ? 'paid' : ((float) $payment->paid_amount > 0 ? 'partial' : 'unpaid');

            $payment->update([
                'agreed_amount' => $validated['agreed_amount'],
                'status' => $status,
            ]);
        } else {
            FreelancerPayment::create([
                'project_id' => $project->id,
                'freelancer_id' => $project->freelancer_id,
                'agreed_amount' => $validated['agreed_amount'],
                'paid_amount' => 0,
                'status' => 'unpaid',
            ]);
        }

        return back()->with('success', 'Agreed amount updated.');
    }

    /**
     * Admin: add a payment installment to a freelancer payment.
     */
    public function addPayment(Request $request, FreelancerPayment $freelancerPayment): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $remaining = (float) $freelancerPayment->agreed_amount - (float) $freelancerPayment->paid_amount;

        $validated = $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:' . number_format($remaining, 2, '.', ''),
            ],
        ]);

        $amount = (float) $validated['amount'];

        FreelancerPaymentLog::create([
            'freelancer_payment_id' => $freelancerPayment->id,
            'amount' => $amount,
        ]);

        $newPaid = (float) $freelancerPayment->paid_amount + $amount;
        $newRemaining = (float) $freelancerPayment->agreed_amount - $newPaid;

        $freelancerPayment->update([
            'paid_amount' => $newPaid,
            'status' => $newRemaining <= 0.001 ? 'paid' : 'partial',
        ]);

        return back()->with('success', 'Payment of TZS ' . number_format($amount, 2) . ' recorded.');
    }

    /**
     * Freelancer: earnings summary page.
     */
    public function earnings(Request $request): View
    {
        $user = $request->user();

        $payments = FreelancerPayment::with(['project', 'logs'])
            ->where('freelancer_id', $user->id)
            ->latest()
            ->get();

        $totalAgreed    = $payments->sum(fn ($p) => (float) $p->agreed_amount);
        $totalPaid      = $payments->sum(fn ($p) => (float) $p->paid_amount);
        $totalRemaining = $totalAgreed - $totalPaid;

        return view('freelancer.earnings', compact('payments', 'totalAgreed', 'totalPaid', 'totalRemaining'));
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()->role === UserRole::ADMIN, 403);
    }
}
