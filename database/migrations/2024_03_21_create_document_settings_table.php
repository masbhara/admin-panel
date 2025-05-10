<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_settings', function (Blueprint $table) {
            $table->id();
            $table->dateTime('submission_deadline')->nullable();
            $table->text('closed_message')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            // Activity log akan otomatis ditangani oleh package spatie/laravel-activitylog
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_settings');
    }
}; 