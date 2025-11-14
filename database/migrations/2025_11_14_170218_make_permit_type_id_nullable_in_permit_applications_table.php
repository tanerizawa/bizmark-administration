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
            // Make permit_type_id nullable for KBLI-based applications
            $table->unsignedBigInteger('permit_type_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permit_applications', function (Blueprint $table) {
            // Revert back to NOT NULL
            $table->unsignedBigInteger('permit_type_id')->nullable(false)->change();
        });
    }
};
