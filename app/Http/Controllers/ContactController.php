<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show(): View
    {
        $settings = Setting::instance();

        return view('contact.index', compact('settings'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email:rfc,dns', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $contact = ContactMessage::create($validated);

        try {
            $settings = Setting::instance();
            $to = $settings->email ?: config('mail.from.address');
            if ($to) {
                Mail::to($to)->send(new ContactMessageMail($contact));
            }
        } catch (\Throwable) {
            // Mail not configured — silently skip
        }

        return back()->with('success', "Thank you, {$contact->name}! Your message has been received. We'll get back to you soon.");
    }
}
