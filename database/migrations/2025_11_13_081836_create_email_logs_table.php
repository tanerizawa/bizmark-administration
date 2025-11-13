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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained('email_campaigns')->cascadeOnDelete();
            $table->foreignId('subscriber_id')->nullable()->constrained('email_subscribers')->cascadeOnDelete();
            $table->string('recipient_email');
            $table->string('subject');
            $table->enum('status', ['sent', 'delivered', 'opened', 'clicked', 'bounced', 'failed'])->default('sent');
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            $table->string('tracking_id')->unique()->nullable(); // For tracking opens/clicks
            $table->string('error_message')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index('campaign_id');
            $table->index('subscriber_id');
            $table->index('status');
            $table->index('tracking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
