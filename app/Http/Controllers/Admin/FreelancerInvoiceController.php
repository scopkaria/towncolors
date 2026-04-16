<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FreelancerInvoice;
use App\Notifications\UserAlertNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FreelancerInvoiceController extends Controller
{
    /**
     * Display all freelancer-submitted invoices.
     */
    public function index(Request $request): View
    {
        $query = FreelancerInvoice::with(['freelancer', 'project'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $invoices = $query->get();

        return view('admin.freelancer-invoices.index', compact('invoices'));
    }

    public function show(FreelancerInvoice $freelancerInvoice): View
    {
        $freelancerInvoice->load(['freelancer', 'project']);

        return view('admin.freelancer-invoices.show', compact('freelancerInvoice'));
    }

    /**
     * Approve a freelancer invoice.
     */
    public function approve(FreelancerInvoice $freelancerInvoice): RedirectResponse
    {
        $freelancerInvoice->loadMissing(['freelancer', 'project']);
        $freelancerInvoice->update([
            'status' => 'approved',
            'rejection_note' => null,
        ]);

        return back()->with('success', 'Invoice approved.');
    }

    /**
     * Reject a freelancer invoice.
     */
    public function reject(Request $request, FreelancerInvoice $freelancerInvoice): RedirectResponse
    {
        $freelancerInvoice->loadMissing(['freelancer', 'project']);

        $validated = $request->validate([
            'rejection_note' => ['required', 'string', 'max:2000'],
        ]);

        $freelancerInvoice->update([
            'status' => 'rejected',
            'rejection_note' => $validated['rejection_note'],
        ]);

        if ($freelancerInvoice->freelancer && $freelancerInvoice->freelancer->exists) {
            $freelancerInvoice->freelancer->notify(new UserAlertNotification(
                'Freelancer invoice rejected',
                'Your invoice for project "' . $freelancerInvoice->project->title . '" was rejected.',
                'Freelancer invoice rejected',
                route('projects.redirect', $freelancerInvoice->project),
                'Open Project',
                projectId: $freelancerInvoice->project_id,
                note: $validated['rejection_note'],
            ));
        }

        return back()->with('success', 'Invoice rejected.');
    }
}
