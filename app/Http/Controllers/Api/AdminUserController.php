<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->user()->role->value !== 'admin') {
                abort(403, 'Admin access required.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('role') && in_array($request->role, ['admin', 'client', 'freelancer'])) {
            $query->where('role', $request->role);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()
            ->paginate(20, ['id', 'name', 'email', 'role', 'profile_image_path', 'created_at', 'email_verified_at']);

        return response()->json($users);
    }

    public function show(User $user)
    {
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->value,
            'phone' => $user->phone,
            'profile_image_path' => $user->profile_image_path,
            'profile_image_url' => $user->profileImageUrl(),
            'email_verified_at' => $user->email_verified_at,
            'created_at' => $user->created_at,
            'has_active_subscription' => $user->hasActiveSubscription(),
            'has_active_trial' => $user->hasActiveTrial(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,client,freelancer'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => UserRole::from($request->role),
        ]);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->value,
            'created_at' => $user->created_at,
        ], 201);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['sometimes', 'in:admin,client,freelancer'],
            'password' => ['sometimes', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('role')) {
            $user->role = UserRole::from($request->role);
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->value,
        ]);
    }

    public function destroy(User $user, Request $request)
    {
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Cannot delete your own account.'], 422);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json(null, 204);
    }

    public function freelancers()
    {
        $freelancers = User::where('role', 'freelancer')
            ->get(['id', 'name', 'email', 'profile_image_path']);

        return response()->json($freelancers);
    }
}
