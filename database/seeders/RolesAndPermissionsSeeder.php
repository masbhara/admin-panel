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
            
            // Document Settings
            'view document settings',
            'create document settings',
            'edit document settings',
            'delete document settings',
            'manage document settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        // Super Admin has all permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdminRole->syncPermissions(Permission::all());

        // Admin juga memiliki semua permission seperti super-admin
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        // Regular user has basic permissions
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions([
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
