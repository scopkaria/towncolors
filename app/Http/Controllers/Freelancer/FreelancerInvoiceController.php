<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\FreelancerInvoice;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
     * Store a newly uploaded invoice PDF.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'invoice'    => ['required', 'file', 'mimes:pdf', 'max:10240'], // 10 MB max
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

        $file = $request->file('invoice');
        $filename = Str::uuid() . '.pdf';
        $path = $file->storeAs(
            'freelancer-invoices/' . $request->user()->id,
            $filename,
            'local'   // private disk — not publicly accessible
        );

        FreelancerInvoice::create([
            'freelancer_id' => $request->user()->id,
            'project_id'    => $project->id,
            'file_path'     => $path,
            'status'        => 'pending',
        ]);

        return back()->with('success', 'Invoice submitted successfully.');
    }
}
