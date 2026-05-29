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
        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('wa_phone')->index();
            $table->string('wa_name')->nullable();
            $table->enum('status', ['bot', 'handoff', 'resolved'])->default('bot');
            $table->json('context')->nullable();
            $table->foreignId('service_inquiry_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_conversations');
    }
};
