<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Log status awal
        $documents = DB::table('documents')->get();
        Log::info('Fix document_form_id migration starting', [
            'total_documents' => $documents->count(),
            'documents_with_form_id' => $documents->whereNotNull('document_form_id')->count(),
            'documents_without_form_id' => $documents->whereNull('document_form_id')->count()
        ]);

        // Periksa apakah foreign key constraint ada
        $foreignKeys = DB::select("
            SELECT *
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = DATABASE()
            AND TABLE_NAME = 'documents'
            AND CONSTRAINT_NAME = 'documents_document_form_id_foreign'
        ");

        // Hapus constraint jika ada
        if (!empty($foreignKeys)) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropForeign(['document_form_id']);
            });
            Log::info('Dropped foreign key constraint documents_document_form_id_foreign');
        }

        // Ubah tipe kolom document_form_id untuk memastikan kompatibilitas
        Schema::table('documents', function (Blueprint $table) {
            // Ubah kolom menjadi nullable dulu untuk keamanan
            $table->unsignedBigInteger('document_form_id')->nullable()->change();
        });
        Log::info('Changed document_form_id to nullable');

        // Tambahkan kembali foreign key constraint
        Schema::table('documents', function (Blueprint $table) {
            $table->foreign('document_form_id')
                  ->references('id')
                  ->on('document_forms')
                  ->onDelete('cascade');
        });
        Log::info('Added foreign key constraint back');

        // Periksa apakah ada form dokumen default
        $defaultForm = DB::table('document_forms')->first();
        if ($defaultForm) {
            // Update semua dokumen yang tidak memiliki document_form_id
            $updated = DB::table('documents')
                ->whereNull('document_form_id')
                ->update(['document_form_id' => $defaultForm->id]);
            
            Log::info('Updated documents without form_id', [
                'default_form_id' => $defaultForm->id,
                'updated_count' => $updated
            ]);
        } else {
            // Buat form dokumen default jika tidak ada
            $defaultFormId = DB::table('document_forms')->insertGetId([
                'title' => 'Form Dokumen Default',
                'description' => 'Form dokumen default untuk dokumen lama',
                'slug' => 'default-form',
                'is_active' => true,
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Update semua dokumen yang tidak memiliki document_form_id
            $updated = DB::table('documents')
                ->whereNull('document_form_id')
                ->update(['document_form_id' => $defaultFormId]);
            
            Log::info('Created default form and updated documents', [
                'default_form_id' => $defaultFormId,
                'updated_count' => $updated
            ]);
        }

        // Ubah kolom menjadi wajib setelah semua dokumen memiliki form_id
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedBigInteger('document_form_id')->nullable(false)->change();
        });
        Log::info('Changed document_form_id to required (not nullable)');

        // Log status akhir
        $documentsAfter = DB::table('documents')->get();
        Log::info('Fix document_form_id migration completed', [
            'total_documents' => $documentsAfter->count(),
            'documents_with_form_id' => $documentsAfter->whereNotNull('document_form_id')->count(),
            'documents_without_form_id' => $documentsAfter->whereNull('document_form_id')->count()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus foreign key constraint
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['document_form_id']);
        });

        // Kembalikan kolom menjadi nullable
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedBigInteger('document_form_id')->nullable()->change();
        });

        // Tambahkan kembali foreign key dengan nullOnDelete
        Schema::table('documents', function (Blueprint $table) {
            $table->foreign('document_form_id')
                  ->references('id')
                  ->on('document_forms')
                  ->nullOnDelete();
        });
    }
};
