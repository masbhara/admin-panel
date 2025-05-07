<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')
            ->latest()
            ->paginate(10)
            ->through(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'roles' => $user->roles->map(fn($role) => [
                    'id' => $role->id,
                    'name' => $role->name
                ]),
                'avatar_url' => $user->avatar_url,
                'created_at' => $user->created_at->format('d M Y'),
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'roles' => Role::all()->map(fn ($role) => [
                'id' => $role->id,
                'name' => $role->name,
            ]),
            'can' => [
                'edit_users' => auth()->user()->can('edit users'),
                'delete_users' => auth()->user()->can('delete users'),
            ],
        ]);
    }

    public function create()
    {
        $roles = Role::all()->map(fn ($role) => [
            'id' => $role->id,
            'name' => $role->name,
        ]);

        return Inertia::render('Admin/Users/Create', [
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', Password::defaults()],
            'roles' => 'array',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'status' => 'pending',
        ]);

        if (!empty($validated['roles'])) {
            $user->assignRole($validated['roles']);
        }

        if ($request->hasFile('avatar')) {
            $user->addMediaFromRequest('avatar')
                ->toMediaCollection('avatar');
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all()->map(fn ($role) => [
            'id' => $role->id,
            'name' => $role->name,
        ]);

        return Inertia::render('Admin/Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'phone' => $user->phone,
                'address' => $user->address,
                'roles' => $user->roles->map(fn($role) => [
                    'id' => $role->id,
                    'name' => $role->name
                ]),
                'avatar_url' => $user->avatar_url,
            ],
            'roles' => $roles,
        ]);
    }

    public function show(User $user)
    {
        return Inertia::render('Admin/Users/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'phone' => $user->phone,
                'address' => $user->address,
                'roles' => $user->roles->map(fn($role) => [
                    'id' => $role->id,
                    'name' => $role->name
                ]),
                'avatar_url' => $user->avatar_url,
                'created_at' => $user->created_at->format('d M Y'),
                'updated_at' => $user->updated_at->format('d M Y'),
            ],
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'roles' => 'array',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'password' => ['nullable', Password::defaults()],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        if ($request->hasFile('avatar')) {
            $user->addMediaFromRequest('avatar')
                ->toMediaCollection('avatar');
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function updateStatus(Request $request, User $user)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,pending,blocked',
        ]);

        $user->update(['status' => $validated['status']]);

        return back()->with('success', 'User status updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
