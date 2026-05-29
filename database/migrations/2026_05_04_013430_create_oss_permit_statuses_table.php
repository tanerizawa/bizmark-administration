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
        Schema::create('oss_permit_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('oss_nib', 13)->nullable();
            $table->string('permit_type');
            $table->string('application_number')->nullable();
            $table->string('status_code', 50);
            $table->string('status_label');
            $table->json('raw_response')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamp('status_changed_at')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'status_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oss_permit_statuses');
    }
};
