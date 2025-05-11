<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DocumentSettingsPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan tabel permissions dan roles ada
        if (!Schema::hasTable('permissions') || !Schema::hasTable('roles')) {
            $this->command->error('Tabel permissions atau roles tidak ditemukan. Pastikan package spatie/laravel-permission sudah diinstall dengan benar.');
            return;
        }

        // Buat permission untuk document-settings
        $permissions = [
            'view document settings',
            'create document settings',
            'edit document settings',
            'delete document settings',
            'manage document settings',
        ];

        // Tambahkan permission ke database
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
            $this->command->info("Permission '{$permission}' berhasil dibuat.");
        }

        // Cari role super admin
        $superAdminRole = Role::where('name', 'super admin')
            ->orWhere('name', 'Super Admin')
            ->orWhere('name', 'superadmin')
            ->orWhere('name', 'admin')
            ->first();

        // Jika role super admin tidak ditemukan, buat baru
        if (!$superAdminRole) {
            $superAdminRole = Role::create(['name' => 'super admin', 'guard_name' => 'web']);
            $this->command->info('Role super admin berhasil dibuat.');
        }

        // Berikan semua permission ke super admin
        $superAdminRole->givePermissionTo($permissions);
        $this->command->info('Semua permission document settings berhasil diberikan ke role super admin.');

        // Cari user super admin (biasanya user dengan ID 1 atau email admin)
        $superAdminUser = User::where('id', 1)
            ->orWhere('email', 'admin@domain-anda.com')
            ->first();

        // Jika user super admin ditemukan, berikan role super admin
        if ($superAdminUser) {
            $superAdminUser->assignRole($superAdminRole);
            $this->command->info("User '{$superAdminUser->name}' berhasil diberikan role super admin.");
        } else {
            $this->command->warn('User super admin tidak ditemukan. Pastikan ada user dengan ID 1 atau email admin@domain-anda.com');
        }

        // Tambahkan permission ke route middleware
        $this->command->info('Seeder selesai. Pastikan route document-settings menggunakan middleware permission.');
    }
} 