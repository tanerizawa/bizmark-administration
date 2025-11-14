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
        Schema::create('email_accounts', function (Blueprint $table) {
            $table->id();
            
            // Email address (cs@bizmark.id, sales@bizmark.id, john@bizmark.id)
            $table->string('email')->unique();
            
            // Display name (Customer Service, Sales Team, John Doe)
            $table->string('name');
            
            // Account type: shared (cs@, sales@) or personal (john@)
            $table->enum('type', ['shared', 'personal'])->default('shared');
            
            // Department affiliation
            $table->enum('department', ['general', 'cs', 'sales', 'support', 'finance', 'technical'])
                  ->default('general');
            
            // Description/purpose
            $table->text('description')->nullable();
            
            // Active status
            $table->boolean('is_active')->default(true);
            
            // Auto-reply settings
            $table->boolean('auto_reply_enabled')->default(false);
            $table->text('auto_reply_message')->nullable();
            
            // Default signature for this email account
            $table->text('signature')->nullable();
            
            // Assigned users (JSON array of user IDs)
            $table->json('assigned_users')->nullable();
            
            // Statistics
            $table->integer('total_received')->default(0);
            $table->integer('total_sent')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('email');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_accounts');
    }
};
