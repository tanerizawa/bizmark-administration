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
            // Only add if not exists
            if (!Schema::hasColumn('users', 'company_email')) {
                $table->string('company_email')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('users', 'department')) {
                $table->enum('department', ['general', 'cs', 'sales', 'support', 'finance', 'technical'])
                      ->default('general')
                      ->after('phone');
            }
            
            if (!Schema::hasColumn('users', 'email_signature')) {
                $table->text('email_signature')->nullable()->after('department');
            }
            
            if (!Schema::hasColumn('users', 'job_title')) {
                $table->string('job_title')->nullable()->after('email_signature');
            }
            
            if (!Schema::hasColumn('users', 'notification_preferences')) {
                $table->json('notification_preferences')->nullable()->after('notes');
            }
            
            if (!Schema::hasColumn('users', 'working_hours')) {
                $table->json('working_hours')->nullable()->after('notification_preferences');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['company_email', 'department', 'email_signature', 'job_title', 'notification_preferences', 'working_hours'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
