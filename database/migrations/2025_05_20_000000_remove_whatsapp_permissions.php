<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Menghapus permission-permission yang terkait dengan WhatsApp notifications
        $permissions = [
            'view_whatsapp_notifications',
            'create_whatsapp_notifications',
            'edit_whatsapp_notifications',
            'delete_whatsapp_notifications',
            'manage_whatsapp_settings',
        ];

        // Hapus dari role_has_permissions terlebih dahulu (relasi)
        foreach ($permissions as $permission) {
            $permissionId = DB::table('permissions')
                ->where('name', $permission)
                ->first()
                ?->id;

            if ($permissionId) {
                DB::table('role_has_permissions')
                    ->where('permission_id', $permissionId)
                    ->delete();
            }
        }

        // Kemudian hapus dari tabel permissions
        DB::table('permissions')
            ->whereIn('name', $permissions)
            ->delete();
    }

    public function down(): void
    {
        // Tidak perlu mengembalikan permission yang dihapus
    }
}; 