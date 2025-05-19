<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Daftar permission yang akan dihapus
        $permissions = [
            'view document settings',
            'create document settings',
            'edit document settings',
            'delete document settings',
            'manage document settings',
        ];

        // Hapus dari role_has_permissions terlebih dahulu
        foreach ($permissions as $permission) {
            $permissionId = DB::table('permissions')
                ->where('name', $permission)
                ->first()
                ?->id;

            if ($permissionId) {
                // Hapus dari role_has_permissions
                DB::table('role_has_permissions')
                    ->where('permission_id', $permissionId)
                    ->delete();
            }
        }

        // Hapus dari tabel permissions
        DB::table('permissions')
            ->whereIn('name', $permissions)
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu mengembalikan permission karena tidak relevan lagi
    }
}; 