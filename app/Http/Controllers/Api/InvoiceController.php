<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\FreelancerInvoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role->value === 'client') {
            $invoices = Invoice::whereHas('project', fn($q) => $q->where('client_id', $user->id))
                ->with('project:id,title')
                ->latest()
                ->paginate(15);
        } elseif ($user->role->value === 'admin') {
            $invoices = Invoice::with('project:id,title,client_id', 'project.client:id,name')
                ->latest()
                ->paginate(15);
        } else {
            abort(403);
        }

        return response()->json($invoices);
    }

    public function show(Request $request, Invoice $invoice)
    {
        $user = $request->user();

        $invoice->load(['project:id,title,client_id,freelancer_id', 'project.client:id,name', 'payments']);

        if ($user->role->value === 'client') {
            if ($invoice->project->client_id !== $user->id) {
                abort(403);
            }
        }

        return response()->json($invoice);
    }

    public function freelancerInvoices(Request $request)
    {
        $user = $request->user();

        if ($user->role->value === 'freelancer') {
            $invoices = FreelancerInvoice::where('freelancer_id', $user->id)
                ->with('project:id,title')
                ->latest()
                ->paginate(15);
        } elseif ($user->role->value === 'admin') {
            $invoices = FreelancerInvoice::with(['freelancer:id,name', 'project:id,title'])
                ->latest()
                ->paginate(15);
        } else {
            abort(403);
        }

        return response()->json($invoices);
    }

    public function storeFreelancerInvoice(Request $request)
    {
        $user = $request->user();

        if ($user->role->value !== 'freelancer') {
            abort(403);
        }

        $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $existing = FreelancerInvoice::where('freelancer_id', $user->id)
            ->where('project_id', $request->project_id)
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return response()->json(['message' => 'You already have a pending invoice for this project.'], 422);
        }

        $path = $request->file('file')->store('freelancer-invoices', 'public');

        $invoice = FreelancerInvoice::create([
            'freelancer_id' => $user->id,
            'project_id' => $request->project_id,
            'file_path' => $path,
            'status' => 'pending',
        ]);

        return response()->json($invoice, 201);
    }
}
