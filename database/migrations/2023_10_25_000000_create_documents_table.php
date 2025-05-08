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
        if (!Schema::hasTable('documents')) {
            Schema::create('documents', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('file_path');
                $table->string('file_name');
                $table->bigInteger('file_size');
                $table->string('file_type');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->timestamp('uploaded_at');
                $table->string('status')->default('pending'); // pending, approved, rejected
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
}; 