<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $query = Lead::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $leads        = $query->get();
        $currentStatus = $request->input('status');

        $counts = [
            'all'       => Lead::count(),
            'new'       => Lead::where('status', 'new')->count(),
            'contacted' => Lead::where('status', 'contacted')->count(),
            'converted' => Lead::where('status', 'converted')->count(),
        ];

        return view('admin.leads.index', compact('leads', 'currentStatus', 'counts'));
    }

    public function show(Lead $lead): View
    {
        $lead->load('convertedUser');

        return view('admin.leads.show', compact('lead'));
    }

    public function updateStatus(Request $request, Lead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'status'      => ['required', 'in:new,contacted,converted'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $lead->update($validated);

        return back()->with('success', 'Lead status updated.');
    }

    /**
     * Convert a lead into a registered client account.
     * Creates the User if none exists with that email, then links the lead.
     */
    public function convert(Request $request, Lead $lead): RedirectResponse
    {
        if ($lead->status === 'converted' && $lead->converted_user_id) {
            return back()->with('success', 'This lead has already been converted.');
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8'],
        ]);

        // Reuse existing account if the email is already registered
        $user = User::firstOrCreate(
            ['email' => $lead->email],
            [
                'name'     => $lead->name,
                'role'     => UserRole::CLIENT,
                'password' => bcrypt($request->input('password')),
            ]
        );

        $lead->update([
            'status'             => 'converted',
            'converted_user_id'  => $user->id,
        ]);

        // Bulk-convert any other leads sharing the same email
        Lead::where('email', $lead->email)
            ->whereIn('status', ['new', 'contacted'])
            ->whereNull('converted_user_id')
            ->update([
                'status'            => 'converted',
                'converted_user_id' => $user->id,
            ]);

        return back()->with('success', "Lead converted. Client account for {$user->name} is ready.");
    }
}
