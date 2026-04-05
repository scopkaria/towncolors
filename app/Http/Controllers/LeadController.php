<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Mail\LeadConfirmation;
use App\Mail\NewLeadNotification;
use App\Models\Lead;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LeadController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'email'        => ['required', 'email:rfc', 'max:255'],
            'project_type' => ['nullable', 'string', 'in:' . implode(',', array_keys(Lead::projectTypes()))],
            'message'      => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        // If the authenticated user is submitting, fill in their name/email automatically
        $user = auth()->user();
        if ($user) {
            $validated['name']  = $validated['name']  ?: $user->name;
            $validated['email'] = $validated['email'] ?: $user->email;
        }

        $lead = Lead::create($validated);

        // Auto-link lead if an authenticated client is submitting
        if ($user && $user->role === UserRole::CLIENT) {
            $lead->update([
                'status'            => 'converted',
                'converted_user_id' => $user->id,
            ]);
        }

        try {
            $settings   = Setting::instance();
            $adminEmail = $settings->email ?: config('mail.from.address');

            if ($adminEmail) {
                Mail::to($adminEmail)->send(new NewLeadNotification($lead));
            }

            Mail::to($lead->email)->send(new LeadConfirmation($lead));
        } catch (\Throwable) {
            // Mail not configured — silently skip
        }

        $successMsg = "Thank you, {$lead->name}! We've received your inquiry and will be in touch within 24 hours.";

        // Redirect authenticated users to their dashboard
        if ($user) {
            $dashRoute = $user->role?->value . '.dashboard';
            return redirect()->route($dashRoute)->with('success', $successMsg);
        }

        // Guests → send to login with a prompt to create an account
        session()->flash('lead_success', $successMsg);
        session()->flash('lead_login_prompt', true);

        return redirect()->route('login')->with(
            'status',
            'Your inquiry was received! Sign in or create an account to track your project.'
        );
    }
}
