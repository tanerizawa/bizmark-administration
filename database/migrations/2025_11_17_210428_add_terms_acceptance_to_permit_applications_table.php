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
            $table->timestamp('terms_accepted_at')->nullable()->after('submitted_at');
            $table->string('terms_version', 20)->nullable()->after('terms_accepted_at');
            $table->string('terms_accepted_language', 5)->default('id')->after('terms_version');
            $table->ipAddress('terms_ip_address')->nullable()->after('terms_accepted_language');
            $table->text('terms_user_agent')->nullable()->after('terms_ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permit_applications', function (Blueprint $table) {
            $table->dropColumn([
                'terms_accepted_at',
                'terms_version',
                'terms_accepted_language',
                'terms_ip_address',
                'terms_user_agent'
            ]);
        });
    }
};
