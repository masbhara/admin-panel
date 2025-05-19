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
        Schema::create('document_form_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_form_id')->constrained()->onDelete('cascade');
            $table->boolean('whatsapp_notification_enabled')->default(true);
            $table->string('dripsender_api_key')->nullable();
            $table->string('dripsender_webhook_url')->nullable();
            $table->json('notification_templates')->nullable(); // Menyimpan template untuk setiap event
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_form_notification_settings');
    }
}; 