<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\FreelancerInvoice;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FreelancerInvoiceController extends Controller
{
    /**
     * List all invoices submitted by the authenticated freelancer.
     */
    public function index(Request $request): View
    {
        $invoices = FreelancerInvoice::with('project')
            ->where('freelancer_id', $request->user()->id)
            ->latest()
            ->get();

        // Projects assigned to this freelancer (for the upload form)
        $projects = Project::where('freelancer_id', $request->user()->id)
            ->whereDoesntHave('freelancerInvoices', function ($query) use ($request) {
                $query->where('freelancer_id', $request->user()->id)
                    ->where('status', 'pending');
            })
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('freelancer.freelancer-invoices.index', compact('invoices', 'projects'));
    }

    /**
     * Store a newly created invoice.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'amount'     => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'description' => ['required', 'string', 'min:10', 'max:1000'],
            'due_date'   => ['nullable', 'date', 'after:today'],
        ]);

        // Confirm the project is actually assigned to this freelancer
        $project = Project::where('id', $validated['project_id'])
            ->where('freelancer_id', $request->user()->id)
            ->firstOrFail();

        $hasPendingInvoice = FreelancerInvoice::where('freelancer_id', $request->user()->id)
            ->where('project_id', $project->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingInvoice) {
            return back()
                ->withErrors(['project_id' => 'A pending invoice already exists for this project.'])
                ->withInput();
        }

        FreelancerInvoice::create([
            'freelancer_id' => $request->user()->id,
            'project_id'    => $project->id,
            'invoice_number' => FreelancerInvoice::generateInvoiceNumber(),
            'amount'        => $validated['amount'],
            'description'   => $validated['description'],
            'due_date'      => $validated['due_date'],
            'status'        => 'pending',
        ]);

        return back()->with('success', 'Invoice created successfully and sent to admin for review.');
    }
}
