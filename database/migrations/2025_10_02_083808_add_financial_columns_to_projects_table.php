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
            $table->decimal('contract_value', 15, 2)->default(0)->after('budget')->comment('Nilai kontrak total');
            $table->decimal('down_payment', 15, 2)->default(0)->after('contract_value')->comment('Uang muka (DP)');
            $table->decimal('payment_received', 15, 2)->default(0)->after('down_payment')->comment('Total pembayaran diterima');
            $table->decimal('total_expenses', 15, 2)->default(0)->after('payment_received')->comment('Total pengeluaran');
            $table->decimal('profit_margin', 5, 2)->default(0)->after('total_expenses')->comment('Profit margin (%)');
            $table->text('payment_terms')->nullable()->after('profit_margin')->comment('Termin pembayaran');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid')->after('payment_terms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'contract_value',
                'down_payment',
                'payment_received',
                'total_expenses',
                'profit_margin',
                'payment_terms',
                'payment_status',
            ]);
        });
    }
};
