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
        Schema::create('application_location_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained('permit_applications')->onDelete('cascade');
            
            // Address breakdown
            $table->string('province', 100)->nullable();
            $table->string('city_regency', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('sub_district', 100)->nullable();
            $table->text('full_address')->nullable();
            $table->string('postal_code', 10)->nullable();
            
            // Coordinates
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Zone classification
            $table->enum('zone_type', ['industrial', 'commercial', 'residential', 'mixed', 'special_economic_zone'])->nullable();
            $table->enum('land_status', ['HGB', 'HGU', 'Hak_Milik', 'Girik', 'Sewa', 'Other'])->nullable();
            $table->string('land_certificate_number', 100)->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('application_id');
            $table->index('province');
            $table->index('city_regency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_location_details');
    }
};
