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
            'username' => ['required', 'string', 'max:100', 'alpha_dash', 'unique:users,username'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone'    => ['nullable', 'string', 'max:40', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'start_free_trial' => ['nullable', 'boolean'],
        ], [
            'email.unique' => 'This email is already registered',
            'username.unique' => 'This username is already taken',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'role'     => UserRole::CLIENT,
            'password' => Hash::make($request->password),
        ]);

        if ($request->boolean('start_free_trial') && $user->canStartTrial()) {
            $user->startFreeTrial(5);
        }

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
            'username' => ['required', 'string', 'max:100', 'alpha_dash', 'unique:users,username'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone'    => ['nullable', 'string', 'max:40', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'start_free_trial' => ['nullable', 'boolean'],
        ], [
            'email.unique' => 'This email is already registered',
            'username.unique' => 'This username is already taken',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'role'     => UserRole::FREELANCER,
            'password' => Hash::make($request->password),
        ]);

        if ($request->boolean('start_free_trial') && $user->canStartTrial()) {
            $user->startFreeTrial(5);
        }

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
            'username'     => ['required', 'string', 'max:100', 'alpha_dash', 'unique:users,username'],
            'email'        => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone'        => ['nullable', 'string', 'max:40', 'unique:users,phone'],
            'admin_secret' => ['required', 'string', function ($attribute, $value, $fail) {
                $secret = config('app.admin_register_secret');
                if (empty($secret) || ! hash_equals($secret, $value)) {
                    $fail('The admin invite code is invalid.');
                }
            }],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'email.unique' => 'This email is already registered',
            'username.unique' => 'This username is already taken',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'phone'    => $request->phone,
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
