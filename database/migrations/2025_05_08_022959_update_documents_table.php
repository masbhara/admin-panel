<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('documents')) {
            // Periksa kolom yang ada di tabel
            $columns = DB::select('SHOW COLUMNS FROM documents');
            $columnNames = array_map(function($column) {
                return $column->Field;
            }, $columns);
            
            Schema::table('documents', function (Blueprint $table) use ($columnNames) {
                // Hanya tambahkan kolom jika belum ada
                if (!in_array('description', $columnNames) && !Schema::hasColumn('documents', 'description')) {
                    $table->text('description')->nullable()->after('name');
                }
                
                if (!in_array('metadata', $columnNames) && !Schema::hasColumn('documents', 'metadata')) {
                    $table->json('metadata')->nullable();
                }
            });
        }

        // Tambahkan key document_home_title jika belum ada
        $exists = DB::table('settings')->where('key', 'document_home_title')->exists();
        if (!$exists) {
            DB::table('settings')->insert([
                'key' => 'document_home_title',
                'value' => 'Pengiriman Dokumen Online',
                'group' => 'document',
                'type' => 'text',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hindari error jika kolom-kolom sudah dihapus sebelumnya
        Schema::table('documents', function (Blueprint $table) {
            $columns = Schema::getColumnListing('documents');
            
            if (in_array('description', $columns)) {
                $table->dropColumn('description');
            }
            
            if (in_array('metadata', $columns)) {
                $table->dropColumn('metadata');
            }
        });

        DB::table('settings')->where('key', 'document_home_title')->delete();
    }
};
