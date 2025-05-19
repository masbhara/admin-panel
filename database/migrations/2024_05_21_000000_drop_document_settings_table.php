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
        // Hanya hapus tabel jika ada
        if (Schema::hasTable('document_settings')) {
            Schema::dropIfExists('document_settings');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dalam kasus roll back, create ulang tabel dengan struktur yang sama
        if (!Schema::hasTable('document_settings')) {
            Schema::create('document_settings', function (Blueprint $table) {
                $table->id();
                $table->dateTime('submission_deadline')->nullable();
                $table->text('closed_message')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }
}; 