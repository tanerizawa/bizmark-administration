<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For PostgreSQL, we need to use raw SQL to modify the constraint
        DB::statement('ALTER TABLE application_notes DROP CONSTRAINT IF EXISTS application_notes_author_id_foreign');
        
        Schema::table('application_notes', function (Blueprint $table) {
            // Make author_id nullable
            $table->unsignedBigInteger('author_id')->nullable()->change();
        });
        
        // Add back foreign key but as nullable with ON DELETE SET NULL
        DB::statement('
            ALTER TABLE application_notes 
            ADD CONSTRAINT application_notes_author_id_foreign 
            FOREIGN KEY (author_id) 
            REFERENCES users(id) 
            ON DELETE SET NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE application_notes DROP CONSTRAINT IF EXISTS application_notes_author_id_foreign');
        
        Schema::table('application_notes', function (Blueprint $table) {
            // Make author_id not nullable again
            $table->unsignedBigInteger('author_id')->nullable(false)->change();
        });
        
        // Add back the original foreign key constraint
        DB::statement('
            ALTER TABLE application_notes 
            ADD CONSTRAINT application_notes_author_id_foreign 
            FOREIGN KEY (author_id) 
            REFERENCES users(id) 
            ON DELETE CASCADE
        ');
    }
};
