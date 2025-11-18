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
        Schema::create('business_contexts', function (Blueprint $table) {
            $table->id();
            
            // Relations
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('cascade');
            $table->string('kbli_code', 10);
            $table->foreign('kbli_code')->references('code')->on('kbli')->onDelete('cascade');
            
            // Project Scale
            $table->decimal('land_area', 12, 2)->nullable()->comment('Land area in m²');
            $table->decimal('building_area', 12, 2)->nullable()->comment('Building area in m²');
            $table->integer('number_of_floors')->nullable();
            $table->decimal('investment_value', 20, 2)->nullable()->comment('Investment value in Rupiah');
            
            // Location Details
            $table->string('province', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->enum('zone_type', ['residential', 'commercial', 'industrial', 'mixed', 'special'])->nullable();
            $table->string('coordinates', 100)->nullable()->comment('Lat,Long');
            $table->enum('location_category', ['perkotaan', 'pedesaan', 'kawasan_industri'])->nullable();
            
            // Business Details
            $table->integer('number_of_employees')->nullable();
            $table->string('production_capacity', 100)->nullable();
            $table->decimal('annual_revenue_target', 20, 2)->nullable()->comment('Annual revenue in Rupiah');
            $table->enum('business_scale', ['mikro', 'kecil', 'menengah', 'besar'])->nullable();
            
            // Environmental Factors
            $table->enum('environmental_impact', ['low', 'medium', 'high'])->default('low');
            $table->enum('waste_management', ['minimal', 'standard', 'complex'])->nullable();
            $table->boolean('near_protected_area')->default(false);
            $table->text('environmental_notes')->nullable();
            
            // Legal Status
            $table->enum('ownership_status', ['owned', 'leased', 'partnership'])->nullable();
            $table->json('existing_permits')->nullable()->comment('List of permits already owned');
            $table->enum('urgency_level', ['standard', 'rush'])->default('standard');
            
            // Additional Context
            $table->text('additional_notes')->nullable();
            $table->json('custom_fields')->nullable()->comment('Flexible storage for KBLI-specific data');
            
            // Metadata
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('submitted_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('kbli_code');
            $table->index('client_id');
            $table->index('submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_contexts');
    }
};
