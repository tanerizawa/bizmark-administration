<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('projects')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            Schema::dropIfExists('projects');
            Schema::create('projects', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();

                $table->string('client_name');
                $table->string('client_contact');
                $table->text('client_address')->nullable();

                $table->foreignId('status_id')->constrained('project_statuses');
                $table->foreignId('institution_id')->nullable()->constrained('institutions');

                $table->date('start_date')->nullable();
                $table->date('deadline')->nullable();
                $table->integer('progress_percentage')->default(0);
                $table->decimal('budget', 15, 2)->nullable();
                $table->decimal('actual_cost', 15, 2)->default(0);

                $table->text('notes')->nullable();
                $table->timestamps();
            });

            return;
        }

        if ($driver !== 'sqlite') {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropForeign(['assigned_user_id']);
                $table->dropForeign(['current_status_id']);
                $table->dropForeign(['primary_institution_id']);
            });
        }

        $columnsToDrop = array_values(array_filter([
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
            'is_archived',
        ], fn (string $col) => Schema::hasColumn('projects', $col)));

        $shouldRenameStatus = Schema::hasColumn('projects', 'current_status_id') && ! Schema::hasColumn('projects', 'status_id');
        $shouldRenameInstitution = Schema::hasColumn('projects', 'primary_institution_id') && ! Schema::hasColumn('projects', 'institution_id');

        $hasClientContact = Schema::hasColumn('projects', 'client_contact');
        $hasStartDate = Schema::hasColumn('projects', 'start_date');
        $hasDeadline = Schema::hasColumn('projects', 'deadline');
        $hasProgress = Schema::hasColumn('projects', 'progress_percentage');
        $hasBudget = Schema::hasColumn('projects', 'budget');
        $hasActualCost = Schema::hasColumn('projects', 'actual_cost');

        Schema::table('projects', function (Blueprint $table) use (
            $driver,
            $columnsToDrop,
            $shouldRenameStatus,
            $shouldRenameInstitution,
            $hasClientContact,
            $hasStartDate,
            $hasDeadline,
            $hasProgress,
            $hasBudget,
            $hasActualCost,
        ) {
            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }

            if ($shouldRenameStatus) {
                $table->renameColumn('current_status_id', 'status_id');
            }

            if ($shouldRenameInstitution) {
                $table->renameColumn('primary_institution_id', 'institution_id');
            }

            if (! $hasClientContact) {
                if ($driver === 'sqlite') {
                    $table->string('client_contact');
                } else {
                    $table->string('client_contact')->after('client_name');
                }
            }

            if (! $hasStartDate) {
                if ($driver === 'sqlite') {
                    $table->date('start_date')->nullable();
                } else {
                    $table->date('start_date')->nullable()->after('institution_id');
                }
            }

            if (! $hasDeadline) {
                if ($driver === 'sqlite') {
                    $table->date('deadline')->nullable();
                } else {
                    $table->date('deadline')->nullable()->after('start_date');
                }
            }

            if (! $hasProgress) {
                if ($driver === 'sqlite') {
                    $table->integer('progress_percentage')->default(0);
                } else {
                    $table->integer('progress_percentage')->default(0)->after('deadline');
                }
            }

            if (! $hasBudget) {
                if ($driver === 'sqlite') {
                    $table->decimal('budget', 15, 2)->nullable();
                } else {
                    $table->decimal('budget', 15, 2)->nullable()->after('progress_percentage');
                }
            }

            if (! $hasActualCost) {
                if ($driver === 'sqlite') {
                    $table->decimal('actual_cost', 15, 2)->default(0);
                } else {
                    $table->decimal('actual_cost', 15, 2)->default(0)->after('budget');
                }
            }
        });

        if ($driver !== 'sqlite') {
            Schema::table('projects', function (Blueprint $table) {
                $table->foreign('status_id')->references('id')->on('project_statuses');
                $table->foreign('institution_id')->references('id')->on('institutions');
            });
        }
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
