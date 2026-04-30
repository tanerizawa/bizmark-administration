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
        Schema::create('service_cost_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number', 20)->unique(); // Format: SCR-XXXXXXXX

            // Jenis pemohon: perorangan atau badan
            $table->enum('applicant_type', ['perorangan', 'badan']);

            // Data pemohon umum
            $table->string('name'); // Nama lengkap / Nama perusahaan
            $table->string('email');
            $table->string('phone', 20);
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();

            // Data khusus perorangan
            $table->string('nik', 20)->nullable(); // NIK
            $table->string('occupation')->nullable(); // Pekerjaan

            // Data khusus badan
            $table->string('company_name')->nullable();
            $table->string('npwp', 30)->nullable();
            $table->string('nib', 30)->nullable(); // Nomor Induk Berusaha
            $table->enum('business_type', ['pt', 'cv', 'ud', 'yayasan', 'koperasi', 'lainnya'])->nullable();
            $table->string('business_sector')->nullable(); // Bidang usaha
            $table->string('pic_name')->nullable(); // Person in charge
            $table->string('pic_position')->nullable(); // Jabatan PIC

            // Detail layanan yang diminta
            $table->string('service_category'); // Kategori layanan
            $table->json('services_requested'); // Array of services requested
            $table->text('project_description')->nullable(); // Deskripsi proyek/kebutuhan
            $table->string('project_location')->nullable(); // Lokasi proyek
            $table->decimal('estimated_budget', 15, 2)->nullable(); // Estimasi budget
            $table->string('timeline_expectation')->nullable(); // Ekspektasi waktu

            // Dokumen pendukung
            $table->json('documents')->nullable(); // Array of uploaded document paths

            // Status & tracking
            $table->enum('status', ['pending', 'reviewing', 'quoted', 'accepted', 'rejected', 'cancelled'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->decimal('quoted_price', 15, 2)->nullable();
            $table->text('quote_details')->nullable();
            $table->timestamp('quoted_at')->nullable();
            $table->timestamp('responded_at')->nullable();

            // Sumber inquiry
            $table->string('source')->default('website'); // website, whatsapp, email, referral
            $table->string('referral_code')->nullable();

            // Technical
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('applicant_type');
            $table->index('status');
            $table->index('created_at');
            $table->index('service_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_cost_requests');
    }
};
