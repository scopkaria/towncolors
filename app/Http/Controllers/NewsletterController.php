<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email:rfc', 'max:255'],
        ]);

        $email = strtolower(trim($request->input('email')));

        $already = Subscriber::where('email', $email)->exists();

        if (! $already) {
            Subscriber::create(['email' => $email]);
        }

        return back()->with(
            'newsletter_success',
            $already
                ? 'You are already subscribed — thank you!'
                : 'You\'re subscribed! Welcome aboard.'
        );
    }
}
