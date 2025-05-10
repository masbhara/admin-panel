<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddWhatsappNotificationPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add WhatsApp notification permissions to the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Adding WhatsApp notification permissions...');

        $permissions = [
            'view_whatsapp_notifications',
            'create_whatsapp_notifications',
            'edit_whatsapp_notifications',
            'delete_whatsapp_notifications',
        ];

        $createdPermissions = [];

        foreach ($permissions as $permission) {
            $createdPermissions[] = Permission::firstOrCreate(['name' => $permission]);
            $this->info("Permission '{$permission}' created or already exists.");
        }

        // Assign permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
            $this->info('Permissions assigned to admin role.');
        } else {
            $this->warn('Admin role not found. Permissions not assigned to any role.');
        }

        $this->info('WhatsApp notification permissions have been added successfully!');
    }
} 