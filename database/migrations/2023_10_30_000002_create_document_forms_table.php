<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Admin yang membuat form
            $table->string('title'); // Judul form
            $table->text('description')->nullable(); // Deskripsi form
            $table->string('slug')->unique(); // Slug untuk URL publik
            $table->dateTime('submission_deadline')->nullable(); // Batas waktu pengumpulan
            $table->text('closed_message')->nullable(); // Pesan ketika form ditutup
            $table->boolean('is_active')->default(true); // Status aktif form
            $table->json('metadata')->nullable(); // Data tambahan jika diperlukan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_forms');
    }
}; 