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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama klien/perusahaan
            $table->string('company_name')->nullable(); // Nama perusahaan (jika berbeda)
            $table->string('industry')->nullable(); // Industri/bidang usaha
            $table->string('contact_person')->nullable(); // Nama contact person
            $table->string('email')->nullable(); // Email
            $table->string('phone')->nullable(); // Telepon
            $table->string('mobile')->nullable(); // HP/WhatsApp
            $table->text('address')->nullable(); // Alamat lengkap
            $table->string('city')->nullable(); // Kota
            $table->string('province')->nullable(); // Provinsi
            $table->string('postal_code')->nullable(); // Kode pos
            $table->string('npwp')->nullable(); // NPWP
            $table->string('tax_name')->nullable(); // Nama untuk pajak
            $table->text('tax_address')->nullable(); // Alamat pajak
            $table->enum('client_type', ['individual', 'company', 'government'])->default('company'); // Tipe klien
            $table->enum('status', ['active', 'inactive', 'potential'])->default('active'); // Status
            $table->text('notes')->nullable(); // Catatan
            $table->timestamps();
            $table->softDeletes(); // Soft delete untuk history
            
            // Indexes
            $table->index('name');
            $table->index('email');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
