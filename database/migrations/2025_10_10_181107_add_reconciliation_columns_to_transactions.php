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
        // Add reconciliation columns to project_payments
        Schema::table('project_payments', function (Blueprint $table) {
            $table->boolean('is_reconciled')->default(false)->after('receipt_file');
            $table->timestamp('reconciled_at')->nullable()->after('is_reconciled');
            $table->foreignId('reconciliation_id')->nullable()->constrained('bank_reconciliations')->onDelete('set null')->after('reconciled_at');
        });

        // Add reconciliation columns to project_expenses
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->boolean('is_reconciled')->default(false)->after('is_billable');
            $table->timestamp('reconciled_at')->nullable()->after('is_reconciled');
            $table->foreignId('reconciliation_id')->nullable()->constrained('bank_reconciliations')->onDelete('set null')->after('reconciled_at');
        });

        // Add reconciliation columns to payment_schedules
        Schema::table('payment_schedules', function (Blueprint $table) {
            $table->boolean('is_reconciled')->default(false)->after('reference_number');
            $table->timestamp('reconciled_at')->nullable()->after('is_reconciled');
            $table->foreignId('reconciliation_id')->nullable()->constrained('bank_reconciliations')->onDelete('set null')->after('reconciled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove reconciliation columns from project_payments
        Schema::table('project_payments', function (Blueprint $table) {
            $table->dropForeign(['reconciliation_id']);
            $table->dropColumn(['is_reconciled', 'reconciled_at', 'reconciliation_id']);
        });

        // Remove reconciliation columns from project_expenses
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->dropForeign(['reconciliation_id']);
            $table->dropColumn(['is_reconciled', 'reconciled_at', 'reconciliation_id']);
        });

        // Remove reconciliation columns from payment_schedules
        Schema::table('payment_schedules', function (Blueprint $table) {
            $table->dropForeign(['reconciliation_id']);
            $table->dropColumn(['is_reconciled', 'reconciled_at', 'reconciliation_id']);
        });
    }
};
