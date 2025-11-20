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
        Schema::table('permit_applications', function (Blueprint $table) {
            // Make client_id nullable so that applications can exist without a client
            // (e.g., when client is deleted)
            $table->bigInteger('client_id')->nullable()->change();
        });
        
        // Set client_id to NULL for applications linked to deleted clients
        \DB::statement('UPDATE permit_applications SET client_id = NULL WHERE client_id IN (SELECT id FROM clients WHERE deleted_at IS NOT NULL)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permit_applications', function (Blueprint $table) {
            // Note: Cannot simply revert to NOT NULL as there may be NULL values
            // Would need to handle those records first
            $table->bigInteger('client_id')->nullable(false)->change();
        });
    }
};
