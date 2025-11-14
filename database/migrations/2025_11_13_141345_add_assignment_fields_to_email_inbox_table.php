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
        Schema::table('email_inbox', function (Blueprint $table) {
            // Link to email account
            $table->foreignId('email_account_id')->nullable()->after('to_email')->constrained()->nullOnDelete();
            
            // Department (auto-filled from email_account)
            $table->enum('department', ['general', 'cs', 'sales', 'support', 'finance', 'technical'])
                  ->nullable()
                  ->after('email_account_id');
            
            // Priority level (urgent, high, normal, low)
            $table->enum('priority', ['urgent', 'high', 'normal', 'low'])
                  ->default('normal')
                  ->after('department');
            
            // Status tracking (new, open, pending, resolved, closed)
            $table->enum('status', ['new', 'open', 'pending', 'resolved', 'closed'])
                  ->default('new')
                  ->after('priority');
            
            // Response time tracking
            $table->timestamp('first_responded_at')->nullable()->after('received_at');
            $table->timestamp('resolved_at')->nullable()->after('first_responded_at');
            
            // SLA tracking (in minutes)
            $table->integer('response_time_minutes')->nullable()->after('resolved_at');
            $table->integer('resolution_time_minutes')->nullable()->after('response_time_minutes');
            
            // User who handled this email (may change assigned_to)
            $table->foreignId('handled_by')->nullable()->after('assigned_to')->constrained('users')->nullOnDelete();
            
            // Tags for categorization
            $table->json('tags')->nullable()->after('labels');
            
            // Internal notes (tidak dikirim ke customer)
            $table->text('internal_notes')->nullable()->after('tags');
            
            // Sentiment analysis (positive, neutral, negative)
            $table->enum('sentiment', ['positive', 'neutral', 'negative'])->nullable()->after('internal_notes');
            
            // Add indexes for performance
            $table->index('email_account_id');
            $table->index('department');
            $table->index('status');
            $table->index('priority');
            $table->index(['assigned_to', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::table('email_inbox', function (Blueprint $table) {
            $table->dropForeign(['email_account_id']);
            $table->dropForeign(['handled_by']);
            $table->dropColumn([
                'email_account_id',
                'department',
                'priority',
                'status',
                'first_responded_at',
                'resolved_at',
                'response_time_minutes',
                'resolution_time_minutes',
                'handled_by',
                'tags',
                'internal_notes',
                'sentiment',
            ]);
        });
    }
};
