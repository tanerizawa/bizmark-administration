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
        Schema::create('email_inbox', function (Blueprint $table) {
            $table->id();
            $table->string('message_id')->unique(); // Email Message-ID header
            $table->string('from_email');
            $table->string('from_name')->nullable();
            $table->string('to_email');
            $table->string('subject');
            $table->text('body_html')->nullable();
            $table->text('body_text')->nullable();
            $table->json('attachments')->nullable(); // [{filename, path, size}]
            $table->boolean('is_read')->default(false);
            $table->boolean('is_starred')->default(false);
            $table->enum('category', ['inbox', 'sent', 'trash', 'spam'])->default('inbox');
            $table->json('labels')->nullable(); // Custom labels
            $table->foreignId('replied_to')->nullable()->constrained('email_inbox')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('received_at')->useCurrent();
            $table->timestamps();
            
            $table->index('from_email');
            $table->index('to_email');
            $table->index('is_read');
            $table->index('category');
            $table->index('received_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_inbox');
    }
};
