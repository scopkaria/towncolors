<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscribedClient
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role->value !== 'client') {
            return $next($request);
        }

        if ($user->hasFullAccess()) {
            return $next($request);
        }

        $message = $user->hasUsedTrial()
            ? 'Your free trial has expired. Subscribe to continue.'
            : 'An active subscription or free trial is required to access messages and files.';

        return redirect()->route('client.subscription.show')
            ->with('error', $message);
    }
}