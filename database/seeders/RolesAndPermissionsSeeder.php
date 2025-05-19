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

            // WhatsApp Notifications - Updated to match route middleware
            'view_whatsapp_notifications',
            'create_whatsapp_notifications',
            'edit_whatsapp_notifications',
            'delete_whatsapp_notifications',
            'manage_whatsapp_settings',
            'send_whatsapp_notifications',

            // Media Library
            'view media',
            'upload media',
            'edit media',
            'delete media',
            'manage media',

            // Logs
            'view logs',
            'manage logs',
            'delete logs',

            // Backups
            'view backups',
            'create backups',
            'download backups',
            'delete backups',

            // API
            'view api',
            'manage api',
            'create api tokens',
            'revoke api tokens',

            // Reports
            'view reports',
            'create reports',
            'export reports',
            'print reports',

            // Indicators
            'view indicators',
            'create indicators',
            'edit indicators',
            'delete indicators',
            'manage indicators',

            // Templates
            'view templates',
            'create templates',
            'edit templates',
            'delete templates',
            'manage templates',

            // Comments
            'view comments',
            'create comments',
            'edit comments',
            'delete comments',
            'manage comments',

            // Categories
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            'manage categories',

            // Tags
            'view tags',
            'create tags',
            'edit tags',
            'delete tags',
            'manage tags',
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
            'view comments',
            'create comments',
            'edit comments',
            'view templates',
            'view categories',
            'view tags',
        ]);

        // Assign super-admin role to user ID 1 if exists
        $user = User::find(1);
        if ($user) {
            $user->assignRole('super-admin');
        }

        // Assign admin role to user with email admin@example.com if exists
        $adminUser = User::where('email', 'admin@example.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('admin');
        }
    }
}
