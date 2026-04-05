<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Show the role-chooser landing page.
     */
    public function choose(): View
    {
        return view('auth.register');
    }

    // ── Client ──────────────────────────────────────────────────────────────

    public function createClient(): View
    {
        return view('auth.register-client');
    }

    public function storeClient(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => UserRole::CLIENT,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect($user->dashboardPath());
    }

    // ── Freelancer ───────────────────────────────────────────────────────────

    public function createFreelancer(): View
    {
        return view('auth.register-freelancer');
    }

    public function storeFreelancer(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => UserRole::FREELANCER,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect($user->dashboardPath());
    }

    // ── Super Admin ──────────────────────────────────────────────────────────

    public function createAdmin(): View
    {
        return view('auth.register-admin');
    }

    public function storeAdmin(Request $request): RedirectResponse
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'admin_secret' => ['required', 'string', function ($attribute, $value, $fail) {
                $secret = config('app.admin_register_secret');
                if (empty($secret) || ! hash_equals($secret, $value)) {
                    $fail('The admin invite code is invalid.');
                }
            }],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => UserRole::ADMIN,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect($user->dashboardPath());
    }

    // ── Legacy (kept for backwards compat) ──────────────────────────────────

    /**
     * @deprecated Use role-specific routes instead.
     */
    public function create(): View
    {
        return $this->choose();
    }

    /** @deprecated */
    public function store(Request $request): RedirectResponse
    {
        return $this->storeClient($request);
    }
}
