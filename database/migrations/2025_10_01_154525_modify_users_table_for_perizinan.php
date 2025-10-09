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
            $table->string('full_name')->after('name')->nullable();
            $table->string('position')->after('full_name')->nullable();
            $table->string('phone')->after('email')->nullable();
            $table->enum('role', ['admin', 'staff', 'viewer'])->after('phone')->default('staff');
            $table->boolean('is_active')->after('role')->default(true);
            $table->datetime('last_login_at')->after('is_active')->nullable();
            $table->text('notes')->after('last_login_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'full_name',
                'position', 
                'phone',
                'role',
                'is_active',
                'last_login_at',
                'notes'
            ]);
        });
    }
};
