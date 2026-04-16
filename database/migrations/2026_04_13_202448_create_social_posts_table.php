<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->string('platform'); // telegram, linkedin, twitter, facebook, whatsapp, instagram, gbp
            $table->text('caption')->nullable();
            $table->string('platform_post_id')->nullable();
            $table->string('platform_url')->nullable();
            $table->string('status')->default('pending'); // pending, posted, failed, scheduled
            $table->timestamp('posted_at')->nullable();
            $table->timestamp('scheduled_for')->nullable();
            $table->json('metrics')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['article_id', 'platform']);
            $table->index(['status', 'scheduled_for']);
            $table->index('platform');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_posts');
    }
};
