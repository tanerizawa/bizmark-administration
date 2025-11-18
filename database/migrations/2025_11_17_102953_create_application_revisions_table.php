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
        Schema::create('application_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('permit_applications')->onDelete('cascade');
            $table->integer('revision_number')->default(1);
            
            // Reason for revision
            $table->enum('revision_type', ['technical_adjustment', 'client_request', 'document_incomplete', 'cost_update']);
            $table->text('revision_reason')->nullable();
            $table->foreignId('revised_by_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Snapshot of data at this revision
            $table->jsonb('permits_data')->nullable(); // [{permit_id, permit_name, service_type, estimated_cost, estimated_days}]
            $table->jsonb('project_data')->nullable(); // {location, land_area, building_area, investment_value, zone_type}
            $table->decimal('total_cost', 15, 2)->nullable();
            
            // Status
            $table->enum('status', ['draft', 'pending_client_approval', 'approved', 'rejected'])->default('draft');
            $table->timestamp('client_approved_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('application_id');
            $table->index(['application_id', 'revision_number']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_revisions');
    }
};
