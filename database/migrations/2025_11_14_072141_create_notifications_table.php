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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            
            // Recipient (Polymorphic)
            $table->string('notifiable_type', 50);
            $table->unsignedBigInteger('notifiable_id');
            
            // Notification Content
            $table->string('type', 100);
            $table->string('title', 255);
            $table->text('message');
            $table->string('icon', 50)->nullable();
            
            // Related Entity (Polymorphic)
            $table->string('related_type', 50)->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->string('action_url', 500)->nullable();
            
            // Status
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['notifiable_type', 'notifiable_id']);
            $table->index('read_at');
            $table->index(['related_type', 'related_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
