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
        Schema::create('content_syndications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->string('platform'); // medium, dev.to, hashnode, etc
            $table->string('platform_url')->nullable();
            $table->string('status')->default('pending'); // pending, published, failed
            $table->timestamp('published_at')->nullable();
            $table->json('metrics')->nullable(); // views, likes, comments, etc
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index(['article_id', 'status']);
            $table->index('platform');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_syndications');
    }
};
