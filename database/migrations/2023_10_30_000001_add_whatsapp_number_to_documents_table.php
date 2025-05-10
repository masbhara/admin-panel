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
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'whatsapp_number')) {
                $table->string('whatsapp_number')->nullable()->after('status');
            }
            if (!Schema::hasColumn('documents', 'notification_sent')) {
                $table->boolean('notification_sent')->default(false)->after('whatsapp_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'whatsapp_number')) {
                $table->dropColumn('whatsapp_number');
            }
            if (Schema::hasColumn('documents', 'notification_sent')) {
                $table->dropColumn('notification_sent');
            }
        });
    }
}; 