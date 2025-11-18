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
        Schema::create('application_legality_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('permit_applications')->onDelete('cascade');
            
            // Document type
            $table->enum('document_category', [
                'land_ownership',      // Sertifikat Tanah
                'company_legal',       // Akta, NPWP, NIB
                'existing_permits',    // IMB existing, dll
                'power_of_attorney',   // Surat Kuasa
                'technical',           // Site Plan, Gambar Teknis
                'other'
            ]);
            $table->string('document_name', 200);
            
            // Status
            $table->boolean('is_available')->default(false);
            $table->string('document_number', 100)->nullable();
            $table->date('issued_date')->nullable();
            $table->date('expiry_date')->nullable();
            
            // File attachment
            $table->string('file_path', 500)->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('application_id');
            $table->index('document_category');
            $table->index('is_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_legality_documents');
    }
};
