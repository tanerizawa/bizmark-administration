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
        Schema::table('documents', function (Blueprint $table) {
            $table->boolean('client_visible')->default(false)->after('is_confidential');
            $table->date('document_issued_at')->nullable()->after('client_visible');
            $table->date('document_expires_at')->nullable()->after('document_issued_at');
            $table->string('document_number', 150)->nullable()->after('document_expires_at');
            $table->string('vault_category', 50)->nullable()->after('document_number');
            // vault_category: izin_utama | dokumen_pendukung | laporan | sertifikat | lainnya

            $table->index(['client_visible', 'status'], 'documents_vault_index');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex('documents_vault_index');
            $table->dropColumn([
                'client_visible',
                'document_issued_at',
                'document_expires_at',
                'document_number',
                'vault_category',
            ]);
        });
    }
};
