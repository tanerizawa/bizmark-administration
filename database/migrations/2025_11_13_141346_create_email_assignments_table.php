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
        Schema::create('email_assignments', function (Blueprint $table) {
            $table->id();
            
            // Email account being assigned
            $table->foreignId('email_account_id')->constrained()->onDelete('cascade');
            
            // User assigned to this email
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Role: primary (main handler), backup (secondary), viewer (read-only)
            $table->enum('role', ['primary', 'backup', 'viewer'])->default('primary');
            
            // Permissions
            $table->boolean('can_send')->default(true);
            $table->boolean('can_receive')->default(true);
            $table->boolean('can_delete')->default(false);
            $table->boolean('can_assign_others')->default(false);
            
            // Notification settings
            $table->boolean('notify_on_receive')->default(true);
            $table->boolean('notify_on_reply')->default(false);
            $table->boolean('notify_on_mention')->default(true);
            
            // Priority/order (untuk load balancing jika multiple users)
            $table->integer('priority')->default(0);
            
            // Active status
            $table->boolean('is_active')->default(true);
            
            // Assignment metadata
            $table->foreignId('assigned_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Unique constraint: one user can only have one role per email account
            $table->unique(['email_account_id', 'user_id']);
            
            // Indexes
            $table->index(['email_account_id', 'is_active']);
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_assignments');
    }
};
