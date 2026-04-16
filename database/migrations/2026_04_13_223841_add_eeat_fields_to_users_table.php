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
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('notes');
            $table->string('expertise', 500)->nullable()->after('bio');
            $table->string('linkedin_url', 255)->nullable()->after('expertise');
            $table->string('twitter_url', 255)->nullable()->after('linkedin_url');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bio', 'expertise', 'linkedin_url', 'twitter_url']);
        });
    }
};
