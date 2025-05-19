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
        Log::info('Starting ensure_document_form_id_not_nullable migration');
        
        // Periksa struktur tabel saat ini
        $columns = DB::select("SHOW COLUMNS FROM documents WHERE Field = 'document_form_id'");
        if (!empty($columns)) {
            $column = $columns[0];
            Log::info('Current document_form_id column info:', [
                'field' => $column->Field,
                'type' => $column->Type,
                'null' => $column->Null,
                'key' => $column->Key,
                'default' => $column->Default,
                'extra' => $column->Extra
            ]);
        }
        
        // Periksa apakah ada dokumen tanpa document_form_id
        $documentsWithoutFormId = DB::table('documents')->whereNull('document_form_id')->count();
        Log::info('Documents without form_id:', ['count' => $documentsWithoutFormId]);
        
        if ($documentsWithoutFormId > 0) {
            // Cari form dokumen default
            $defaultForm = DB::table('document_forms')->first();
            
            if ($defaultForm) {
                // Update dokumen tanpa form_id
                DB::table('documents')
                    ->whereNull('document_form_id')
                    ->update(['document_form_id' => $defaultForm->id]);
                
                Log::info('Updated documents without form_id', [
                    'default_form_id' => $defaultForm->id,
                    'updated_count' => $documentsWithoutFormId
                ]);
            } else {
                // Buat form dokumen default
                $defaultFormId = DB::table('document_forms')->insertGetId([
                    'title' => 'Form Dokumen Default',
                    'description' => 'Form dokumen default untuk dokumen lama',
                    'slug' => 'default-form',
                    'is_active' => true,
                    'user_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                // Update dokumen tanpa form_id
                DB::table('documents')
                    ->whereNull('document_form_id')
                    ->update(['document_form_id' => $defaultFormId]);
                
                Log::info('Created default form and updated documents', [
                    'default_form_id' => $defaultFormId,
                    'updated_count' => $documentsWithoutFormId
                ]);
            }
        }
        
        // Periksa foreign key constraint
        $foreignKeys = DB::select("
            SELECT *
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = DATABASE()
            AND TABLE_NAME = 'documents'
            AND CONSTRAINT_NAME = 'documents_document_form_id_foreign'
        ");
        
        // Hapus foreign key jika ada
        if (!empty($foreignKeys)) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropForeign(['document_form_id']);
            });
            Log::info('Dropped foreign key constraint');
        }
        
        // Ubah kolom menjadi tidak nullable
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedBigInteger('document_form_id')->nullable(false)->change();
        });
        Log::info('Changed document_form_id to not nullable');
        
        // Tambahkan kembali foreign key constraint
        Schema::table('documents', function (Blueprint $table) {
            $table->foreign('document_form_id')
                ->references('id')
                ->on('document_forms')
                ->onDelete('cascade');
        });
        Log::info('Added foreign key constraint');
        
        // Verifikasi perubahan
        $columnsAfter = DB::select("SHOW COLUMNS FROM documents WHERE Field = 'document_form_id'");
        if (!empty($columnsAfter)) {
            $columnAfter = $columnsAfter[0];
            Log::info('Updated document_form_id column info:', [
                'field' => $columnAfter->Field,
                'type' => $columnAfter->Type,
                'null' => $columnAfter->Null,
                'key' => $columnAfter->Key,
                'default' => $columnAfter->Default,
                'extra' => $columnAfter->Extra
            ]);
        }
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
        
        // Ubah kolom menjadi nullable
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
