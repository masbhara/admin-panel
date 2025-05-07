<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::with('roles')
            ->paginate(10);

        return Inertia::render('Admin/Permissions/Index', [
            'permissions' => $permissions
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Permissions/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions'],
            'guard_name' => ['required', 'string'],
            'description' => ['nullable', 'string', 'max:255']
        ]);

        Permission::create($validated);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    public function edit(Permission $permission)
    {
        return Inertia::render('Admin/Permissions/Edit', [
            'permission' => $permission
        ]);
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
            'guard_name' => ['required', 'string'],
            'description' => ['nullable', 'string', 'max:255']
        ]);

        $permission->update($validated);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        // Prevent deletion of critical permissions
        $criticalPermissions = [
            'create users',
            'edit users',
            'delete users',
            'manage roles',
            'manage permissions'
        ];

        if (in_array($permission->name, $criticalPermissions)) {
            abort(403, 'Cannot delete critical permissions');
        }

        $permission->delete();

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}
