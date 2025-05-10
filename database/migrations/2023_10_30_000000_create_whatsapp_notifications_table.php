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
        Schema::create('whatsapp_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('template');
            $table->string('event_type'); // Misalnya: document_uploaded, document_approved, dll
            $table->boolean('is_active')->default(true);
            $table->json('variables')->nullable(); // Menyimpan variabel yang dapat digunakan dalam template
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_notifications');
    }
}; 