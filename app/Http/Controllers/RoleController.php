<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Inertia\Inertia;

class RoleController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        // Sementara gunakan hanya middleware untuk auth
        // Tidak perlu authorize lagi karena sudah diatur di middleware
        //$this->authorize('viewAny', Role::class);
        
        $roles = Role::with('permissions')
            ->withCount('users')
            ->paginate(10);

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles,
            'can' => [
                'create_roles' => auth()->user()->can('create roles'),
                'edit_roles' => auth()->user()->can('edit roles'),
                'delete_roles' => auth()->user()->can('delete roles'),
                'assign_permissions' => auth()->user()->can('assign permissions'),
            ],
        ]);
    }

    public function create()
    {
        // Sementara gunakan hanya middleware untuk auth
        //$this->authorize('create', Role::class);
        
        $permissions = Permission::all();

        return Inertia::render('Admin/Roles/Create', [
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Role::class);
        
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'permissions' => ['required', 'array'],
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('admin.roles.index')
            ->with('message', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $this->authorize('update', $role);
        
        $permissions = Permission::all();

        return Inertia::render('Admin/Roles/Edit', [
            'role' => $role->load('permissions'),
            'permissions' => $permissions,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('update', $role);
        
        if ($role->name === 'super-admin') {
            return back()->with('error', 'Super Admin role cannot be modified.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['required', 'array'],
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('admin.roles.index')
            ->with('message', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);
        
        if ($role->name === 'super-admin') {
            return back()->with('error', 'Super Admin role cannot be deleted.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('message', 'Role deleted successfully.');
    }

    public function matrix()
    {
        $this->authorize('viewAny', Role::class);
        
        $roles = Role::all();
        $permissions = Permission::all();
        
        // Create a map of role permissions for efficient lookup
        $rolePermissions = [];
        foreach ($roles as $role) {
            $rolePermissions[$role->id] = $role->permissions->pluck('id')->toArray();
        }

        return Inertia::render('Admin/Permissions/Matrix', [
            'roles' => $roles,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions,
            'can' => [
                'edit_roles' => auth()->user()->can('edit roles'),
                'assign_permissions' => auth()->user()->can('assign permissions'),
            ],
        ]);
    }

    public function togglePermission(Request $request)
    {
        $request->validate([
            'role' => ['required', 'exists:roles,id'],
            'permission' => ['required', 'exists:permissions,id'],
            'action' => ['required', 'in:give,revoke'],
        ]);

        $role = Role::findOrFail($request->role);
        $permission = Permission::findOrFail($request->permission);

        $this->authorize('update', $role);

        if ($role->name === 'super-admin') {
            return back()->with('error', 'Super Admin role permissions cannot be modified.');
        }

        if ($request->action === 'give') {
            $role->givePermissionTo($permission);
        } else {
            $role->revokePermissionTo($permission);
        }

        return back()->with('message', 'Permission updated successfully.');
    }

    public function toggleAllPermissions(Request $request)
    {
        $request->validate([
            'role' => ['required', 'exists:roles,id'],
            'action' => ['required', 'in:give,revoke'],
        ]);

        $role = Role::findOrFail($request->role);
        
        $this->authorize('update', $role);
        
        if ($role->name === 'super-admin') {
            return response()->json([
                'message' => 'Super Admin role permissions cannot be modified.'
            ], 422);
        }

        $permissions = Permission::all();
        
        if ($request->action === 'give') {
            // Berikan semua permission
            $role->syncPermissions($permissions);
        } else {
            // Cabut semua permission
            $role->syncPermissions([]);
        }

        return response()->json([
            'message' => 'Permissions updated successfully.'
        ]);
    }
}
