<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')
            ->withCount('users')
            ->paginate(10);

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles
        ]);
    }

    public function create()
    {
        $permissions = Permission::all();

        return Inertia::render('Admin/Roles/Create', [
            'permissions' => $permissions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'guard_name' => ['required', 'string'],
            'permissions' => ['required', 'array']
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => $validated['guard_name']
        ]);

        $role->syncPermissions($validated['permissions']);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::all();

        return Inertia::render('Admin/Roles/Edit', [
            'role' => $role,
            'permissions' => $permissions
        ]);
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'admin') {
            abort(403, 'Cannot modify admin role');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'guard_name' => ['required', 'string'],
            'permissions' => ['required', 'array']
        ]);

        $role->update([
            'name' => $validated['name'],
            'guard_name' => $validated['guard_name']
        ]);

        $role->syncPermissions($validated['permissions']);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'admin') {
            abort(403, 'Cannot delete admin role');
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
