<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        // Sementara gunakan hanya middleware untuk auth
        // Tidak perlu authorize lagi karena sudah diatur di middleware
        //$this->authorize('viewAny', Permission::class);

        return Inertia::render('Admin/Permissions/Index', [
            'permissions' => Permission::with('roles')->paginate(10),
            'can' => [
                'create_permissions' => auth()->user()->can('create permissions'),
                'edit_permissions' => auth()->user()->can('edit permissions'),
                'delete_permissions' => auth()->user()->can('delete permissions'),
                'assign_permissions' => auth()->user()->can('assign permissions'),
            ],
        ]);
    }

    public function create()
    {
        // Sementara gunakan hanya middleware untuk auth
        //$this->authorize('create', Permission::class);

        return Inertia::render('Admin/Permissions/Create', [
            'roles' => Role::all(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Permission::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions',
        ]);

        Permission::create(['name' => $validated['name']]);

        return redirect()->route('admin.permissions.index')
            ->with('message', 'Permission created successfully.');
    }

    public function edit(Permission $permission)
    {
        $this->authorize('update', $permission);

        return Inertia::render('Admin/Permissions/Edit', [
            'permission' => $permission,
            'roles' => Role::all(),
        ]);
    }

    public function update(Request $request, Permission $permission)
    {
        $this->authorize('update', $permission);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $validated['name']]);

        return redirect()->route('admin.permissions.index')
            ->with('message', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $this->authorize('delete', $permission);

        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('message', 'Permission deleted successfully.');
    }
}
