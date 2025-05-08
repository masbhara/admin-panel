<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddDocumentPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:add-document';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menambahkan permission untuk manajemen dokumen';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $permissions = [
            'view-documents',
            'create-documents',
            'edit-documents',
            'delete-documents',
        ];

        $this->info('Menambahkan permission untuk dokumen...');

        // Buat permission jika belum ada
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
            $this->info("Permission '{$permissionName}' berhasil dibuat.");
        }

        // Tambahkan permission ke role admin
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
            $this->info('Semua permission dokumen berhasil ditambahkan ke role admin.');
        } else {
            $this->warn('Role admin tidak ditemukan!');
        }

        $this->info('Selesai!');
    }
} 