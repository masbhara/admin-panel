<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Users
            'view users',
            'create users',
            'edit users',
            'delete users',
            'impersonate users',
            'manage user status',
            
            // Roles
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'assign roles',
            
            // Permissions
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            'assign permissions',
            
            // Activities
            'view activities',
            'manage activities',
            
            // Settings
            'view settings',
            'edit settings',
            'delete settings',
            
            // Profile
            'edit profile',
            'delete profile',
            'manage avatar',
            
            // Dashboard
            'view dashboard',
            'manage dashboard',
            
            // Documents
            'view-documents',
            'create-documents',
            'edit-documents',
            'delete-documents',
            'approve-documents',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        // Super Admin has all permissions
        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo(Permission::all());

        // Admin has most permissions except critical ones
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo([
            // Users
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage user status',
            
            // Roles
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            
            // Permissions
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            'assign permissions',
            
            // Activities
            'view activities',
            'manage activities',
            
            // Settings
            'view settings',
            'edit settings',
            
            // Profile
            'edit profile',
            'manage avatar',
            
            // Dashboard
            'view dashboard',
            'manage dashboard',
            
            // Documents
            'view-documents',
            'create-documents',
            'edit-documents',
            'delete-documents',
            'approve-documents',
        ]);

        // Regular user has basic permissions
        $role = Role::create(['name' => 'user']);
        $role->givePermissionTo([
            'view activities',
            'edit profile',
            'manage avatar',
            'view dashboard',
            'view-documents',
            'create-documents',
        ]);

        // Assign super-admin role to user ID 1
        $user = User::find(1);
        if ($user) {
            $user->assignRole('super-admin');
        }
    }
}
