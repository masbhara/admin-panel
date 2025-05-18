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
        // Hapus semua dokumen yang tidak memiliki document_form_id (null)
        DB::table('documents')->whereNull('document_form_id')->delete();

        // Periksa apakah foreign key constraint ada
        $foreignKeys = DB::select("
            SELECT *
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = DATABASE()
            AND TABLE_NAME = 'documents'
            AND CONSTRAINT_NAME = 'documents_document_form_id_foreign'
        ");

        Schema::table('documents', function (Blueprint $table) use ($foreignKeys) {
            // Hapus foreign key constraint yang ada terlebih dahulu jika ada
            if (!empty($foreignKeys)) {
                $table->dropForeign(['document_form_id']);
            }
            
            // Ubah kolom menjadi non-nullable (required)
            $table->unsignedBigInteger('document_form_id')->nullable(false)->change();
            
            // Tambahkan foreign key constraint
            $table->foreign('document_form_id')
                  ->references('id')
                  ->on('document_forms')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Hapus foreign key constraint
            $table->dropForeign(['document_form_id']);
            
            // Kembalikan kolom menjadi nullable
            $table->unsignedBigInteger('document_form_id')->nullable()->change();
            
            // Tambahkan kembali foreign key dengan nullOnDelete
            $table->foreign('document_form_id')
                  ->references('id')
                  ->on('document_forms')
                  ->onDelete('set null');
        });
    }
};
