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
        Schema::create('permit_expiry_monitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('project_permit_id')->nullable()->constrained('project_permits')->nullOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('permit_type', 200);
            $table->string('permit_number', 100)->nullable();
            $table->date('issued_at')->nullable();
            $table->date('expires_at');
            $table->enum('status', ['active', 'expiring_soon', 'expired', 'renewed'])->default('active');
            $table->boolean('notified_90')->default(false);
            $table->boolean('notified_30')->default(false);
            $table->boolean('notified_7')->default(false);
            $table->timestamp('last_notified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['expires_at', 'status']);
            $table->index('client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permit_expiry_monitors');
    }
};
