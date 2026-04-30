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
        Schema::table('auto_post_schedules', function (Blueprint $table) {
            if (! Schema::hasColumn('auto_post_schedules', 'article_id')) {
                $table->foreignId('article_id')
                    ->nullable()
                    ->after('topic_id')
                    ->constrained('articles')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('auto_post_schedules', 'article_id')) {
            return;
        }

        Schema::table('auto_post_schedules', function (Blueprint $table) {
            $table->dropConstrainedForeignId('article_id');
        });
    }
};
