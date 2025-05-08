<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class CheckPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-permissions {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check permissions for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'admin@example.com';
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }
        
        $this->info('User: ' . $user->name);
        $this->info('Email: ' . $user->email);
        $this->info('Roles: ' . implode(', ', $user->getRoleNames()->toArray()));
        
        // Check specific permission
        $this->info('Has view-documents permission: ' . ($user->hasPermissionTo('view-documents') ? 'Ya' : 'Tidak'));
        
        // Show all permissions
        $this->info('All Permissions:');
        $allPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        foreach ($allPermissions as $permission) {
            $this->line(' - ' . $permission);
        }
        
        // List all available permissions in system
        $this->info('Available Permissions in System:');
        $availablePermissions = Permission::all()->pluck('name')->toArray();
        foreach ($availablePermissions as $permission) {
            $this->line(' - ' . $permission . ': ' . ($user->hasPermissionTo($permission) ? 'Ya' : 'Tidak'));
        }
        
        return 0;
    }
}
