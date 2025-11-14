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
        Schema::table('users', function (Blueprint $table) {
            // Check and add columns only if they don't exist
            if (!Schema::hasColumn('users', 'username')) {
                // Username for login (unique, nullable for backward compatibility)
                $table->string('username')->nullable()->unique()->after('name');
            }
            
            if (!Schema::hasColumn('users', 'employee_id')) {
                // Employee ID/NIP (unique if provided)
                $table->string('employee_id')->nullable()->unique()->after('phone');
            }
        });

        // Change department from enum to string for more flexibility
        DB::statement("ALTER TABLE users ALTER COLUMN department TYPE varchar(255) USING department::varchar");
        DB::statement("ALTER TABLE users ALTER COLUMN department DROP DEFAULT");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn('username');
            }
            if (Schema::hasColumn('users', 'employee_id')) {
                $table->dropColumn('employee_id');
            }
        });

        // Revert department back to enum
        DB::statement("ALTER TABLE users ALTER COLUMN department TYPE varchar(255)");
        DB::statement("ALTER TABLE users ALTER COLUMN department SET DEFAULT 'general'");
    }
};
