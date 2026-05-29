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
        Schema::create('regulatory_changes', function (Blueprint $table) {
            $table->id();
            $table->string('source_url');
            $table->string('document_number', 100)->nullable();
            $table->string('title');
            $table->date('published_at');
            $table->text('summary_id')->nullable();
            $table->text('summary_en')->nullable();
            $table->json('affected_service_categories')->nullable();
            $table->float('relevance_score')->default(0);
            $table->boolean('notified')->default(false);
            $table->string('document_hash', 64)->unique();
            $table->timestamps();

            $table->index(['relevance_score', 'notified']);
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regulatory_changes');
    }
};
