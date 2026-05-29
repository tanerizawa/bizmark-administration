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
        Schema::table('clients', function (Blueprint $table) {
            $table->boolean('notif_email')->default(true)->after('profile_picture');
            $table->boolean('notif_whatsapp')->default(false)->after('notif_email');
            $table->boolean('notif_push')->default(false)->after('notif_whatsapp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['notif_email', 'notif_whatsapp', 'notif_push']);
        });
    }
};
