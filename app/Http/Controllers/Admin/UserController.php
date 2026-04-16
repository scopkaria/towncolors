<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewUserCredentialsNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->string('role')))
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = [UserRole::CLIENT, UserRole::FREELANCER];

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:40', 'unique:users,phone'],
            'role' => ['required', 'in:client,freelancer'],
            'temporary_password' => ['required', 'string', 'min:8', 'max:255'],
        ], [
            'email.unique' => 'This email is already registered',
            'username.unique' => 'This username is already taken',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['temporary_password']),
            'must_change_password' => true,
            'onboarding_completed' => false,
        ]);

        $user->notify(new NewUserCredentialsNotification(
            email: $validated['email'],
            temporaryPassword: $validated['temporary_password'],
            loginUrl: route('login')
        ));

        return redirect()->route('admin.users.index')->with('success', 'User created and credentials sent successfully.');
    }
}