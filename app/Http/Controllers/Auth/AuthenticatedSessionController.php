<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the role-chooser landing page.
     */
    public function choose(): View
    {
        return view('auth.login');
    }

    public function createClient(): View
    {
        return view('auth.login-client');
    }

    public function createFreelancer(): View
    {
        return view('auth.login-freelancer');
    }

    public function createAdmin(): View
    {
        return view('auth.login-admin');
    }

    /**
     * @deprecated Use role-specific routes instead.
     */
    public function create(): View
    {
        return $this->choose();
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended($request->user()->dashboardPath());
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
