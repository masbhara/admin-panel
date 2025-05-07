<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Role management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            
            // Permission management
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            
            // Activity logs
            'view activity logs',
            
            // Settings
            'view settings',
            'edit settings',
            
            // Profile
            'edit profile',
            'delete profile',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Admin role
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());
        
        // Manager role
        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'view users',
            'edit users',
            'view roles',
            'view permissions',
            'view activity logs',
            'view settings',
            'edit profile',
            'delete profile',
        ]);
        
        // User role
        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo([
            'edit profile',
            'delete profile',
        ]);
    }
}
