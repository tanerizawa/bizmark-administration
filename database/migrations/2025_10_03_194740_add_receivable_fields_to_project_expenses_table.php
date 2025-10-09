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
        Schema::table('project_expenses', function (Blueprint $table) {
            // Tambah field untuk tracking kasbon/piutang internal
            $table->boolean('is_receivable')->default(false)->after('is_billable')
                ->comment('Apakah pengeluaran ini merupakan kasbon/piutang yang perlu dikembalikan oleh karyawan/pihak internal');
            $table->string('receivable_from')->nullable()->after('is_receivable')
                ->comment('Nama karyawan/pihak yang berhutang');
            $table->enum('receivable_status', ['pending', 'partial', 'paid'])->default('pending')->after('receivable_from')
                ->comment('Status pembayaran kasbon: pending, partial, paid');
            $table->decimal('receivable_paid_amount', 15, 2)->default(0)->after('receivable_status')
                ->comment('Jumlah yang sudah dibayar/dikembalikan');
            $table->text('receivable_notes')->nullable()->after('receivable_paid_amount')
                ->comment('Catatan pembayaran kasbon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->dropColumn([
                'is_receivable',
                'receivable_from',
                'receivable_status',
                'receivable_paid_amount',
                'receivable_notes'
            ]);
        });
    }
};
