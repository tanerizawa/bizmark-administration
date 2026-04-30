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
        Schema::table('shapefile_projects', function (Blueprint $table) {
            $table->string('company_name', 150)->nullable()->after('name');
            $table->string('contact_person', 100)->nullable()->after('company_name');
            $table->string('email', 150)->nullable()->after('contact_person');
            $table->string('phone', 30)->nullable()->after('email');
            $table->timestamp('agreed_terms_at')->nullable()->after('session_token');
            $table->foreignId('service_inquiry_id')->nullable()->after('agreed_terms_at')
                ->constrained('service_inquiries')->nullOnDelete();
            $table->ipAddress('ip_address')->nullable()->after('service_inquiry_id');
            $table->text('user_agent')->nullable()->after('ip_address');

            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shapefile_projects', function (Blueprint $table) {
            $table->dropForeign(['service_inquiry_id']);
            $table->dropIndex(['email']);
            $table->dropColumn([
                'company_name', 'contact_person', 'email', 'phone',
                'agreed_terms_at', 'service_inquiry_id', 'ip_address', 'user_agent',
            ]);
        });
    }
};
