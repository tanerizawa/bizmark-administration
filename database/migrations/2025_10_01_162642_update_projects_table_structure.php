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
        Schema::table('projects', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['assigned_user_id']);
            $table->dropForeign(['current_status_id']);
            $table->dropForeign(['primary_institution_id']);
        });

        Schema::table('projects', function (Blueprint $table) {
            // Drop columns we don't need
            $table->dropColumn([
                'code',
                'client_company',
                'client_industry_category',
                'client_phone',
                'client_email',
                'permit_type',
                'sub_permits',
                'project_location',
                'project_value',
                'contract_date',
                'target_completion_date',
                'actual_completion_date',
                'assigned_user_id',
                'is_urgent',
                'is_archived'
            ]);

            // Rename columns to match our expected structure
            $table->renameColumn('current_status_id', 'status_id');
            $table->renameColumn('primary_institution_id', 'institution_id');

            // Add new columns
            $table->string('client_contact')->after('client_name');
            $table->date('start_date')->nullable()->after('institution_id');
            $table->date('deadline')->nullable()->after('start_date');
            $table->integer('progress_percentage')->default(0)->after('deadline');
            $table->decimal('budget', 15, 2)->nullable()->after('progress_percentage');
            $table->decimal('actual_cost', 15, 2)->default(0)->after('budget');
        });

        Schema::table('projects', function (Blueprint $table) {
            // Re-add foreign key constraints for the renamed columns
            $table->foreign('status_id')->references('id')->on('project_statuses');
            $table->foreign('institution_id')->references('id')->on('institutions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Add back the columns we dropped
            $table->string('code')->unique()->after('id');
            $table->string('client_company')->nullable()->after('client_name');
            $table->string('client_industry_category')->nullable()->after('client_company');
            $table->string('client_phone')->nullable()->after('client_address');
            $table->string('client_email')->nullable()->after('client_phone');
            $table->string('permit_type')->after('client_email');
            $table->json('sub_permits')->nullable()->after('permit_type');
            $table->text('project_location')->nullable()->after('sub_permits');
            $table->decimal('project_value', 15, 2)->nullable()->after('project_location');
            $table->date('contract_date')->nullable()->after('project_value');
            $table->date('target_completion_date')->nullable()->after('contract_date');
            $table->date('actual_completion_date')->nullable()->after('target_completion_date');
            $table->unsignedBigInteger('assigned_user_id')->nullable()->after('actual_completion_date');
            $table->boolean('is_urgent')->default(false)->after('notes');
            $table->boolean('is_archived')->default(false)->after('is_urgent');

            // Rename columns back
            $table->renameColumn('status_id', 'current_status_id');
            $table->renameColumn('institution_id', 'primary_institution_id');

            // Drop new columns
            $table->dropColumn([
                'client_contact',
                'start_date',
                'deadline',
                'progress_percentage',
                'budget',
                'actual_cost'
            ]);

            // Add foreign key constraints back
            $table->foreign('assigned_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }
};
