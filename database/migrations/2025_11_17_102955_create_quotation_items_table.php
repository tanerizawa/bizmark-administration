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
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('permit_applications')->onDelete('cascade');
            $table->foreignId('revision_id')->nullable()->constrained('application_revisions')->onDelete('cascade');
            
            // Item details
            $table->enum('item_type', ['permit', 'consultation', 'survey', 'processing', 'other'])->default('permit');
            $table->foreignId('permit_type_id')->nullable()->constrained('permit_types')->onDelete('set null');
            
            $table->string('item_name', 200);
            $table->text('description')->nullable();
            
            // Pricing
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 15, 2)->default(0);
            
            // Service type
            $table->enum('service_type', ['bizmark', 'owned', 'self'])->default('bizmark');
            
            // Time estimation
            $table->integer('estimated_days')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('application_id');
            $table->index('revision_id');
            $table->index('item_type');
            $table->index('permit_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};
