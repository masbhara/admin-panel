<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Document permissions
        $permissions = [
            'view-documents',
            'create-documents',
            'edit-documents',
            'delete-documents',
            'manage-documents',
            'view document forms',
            'create document forms',
            'edit document forms',
            'delete document forms'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Assign all permissions to super-admin
        $superAdminRole->givePermissionTo(Permission::all());

        // Assign document permissions to admin role
        $adminRole->givePermissionTo($permissions);

        // Assign basic permissions to user role
        $userRole->givePermissionTo([
            'view-documents',
            'create-documents',
            'edit-documents'
        ]);
    }
} 