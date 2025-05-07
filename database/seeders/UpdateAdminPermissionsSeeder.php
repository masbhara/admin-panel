<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UpdateAdminPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Get admin role
        $adminRole = Role::where('name', 'admin')->first();
        
        if ($adminRole) {
            // Update admin permissions
            $adminRole->givePermissionTo([
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
            ]);
            
            $this->command->info('Admin permissions updated successfully.');
        } else {
            $this->command->error('Admin role not found.');
        }
    }
} 