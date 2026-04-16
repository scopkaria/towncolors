<?php

namespace App\Http\Controllers\Client;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TrialController extends Controller
{
    public function start(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->role === UserRole::CLIENT, 403);

        if (! $user->canStartTrial()) {
            return back()->with('error', 'Free trial already used for this account');
        }

        $user->startFreeTrial(5);

        return back()->with('success', 'Your free trial is now active for 5 days.');
    }
}
