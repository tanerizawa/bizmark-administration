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
        Schema::table('project_payments', function (Blueprint $table) {
            // Drop existing foreign key constraint
            $table->dropForeign(['project_id']);
            
            // Make project_id nullable
            $table->foreignId('project_id')->nullable()->change();
            
            // Re-add foreign key with SET NULL on delete
            $table->foreign('project_id')
                  ->references('id')
                  ->on('projects')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_payments', function (Blueprint $table) {
            // Drop the SET NULL constraint
            $table->dropForeign(['project_id']);
            
            // Make project_id NOT nullable again
            $table->foreignId('project_id')->nullable(false)->change();
            
            // Re-add foreign key with CASCADE
            $table->foreign('project_id')
                  ->references('id')
                  ->on('projects')
                  ->onDelete('cascade');
        });
    }
};
