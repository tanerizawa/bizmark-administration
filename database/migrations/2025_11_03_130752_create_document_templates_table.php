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
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('permit_type', [
                'pertek_bpn',
                'ukl_upl',
                'amdal',
                'imb',
                'pbg',
                'slf',
                'siup',
                'tdp',
                'npwp',
                'other'
            ]);
            $table->text('description')->nullable();
            $table->string('file_name');
            $table->string('file_path');
            $table->unsignedInteger('file_size'); // bytes
            $table->string('mime_type', 50);
            $table->unsignedSmallInteger('page_count')->nullable();
            $table->json('required_fields')->nullable(); // ['project_name', 'location', etc]
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('permit_type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }
};
