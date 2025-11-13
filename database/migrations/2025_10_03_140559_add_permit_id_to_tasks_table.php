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
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('project_permit_id')->nullable()->after('depends_on_task_id')->constrained('project_permits')->nullOnDelete();
            $table->index('project_permit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['project_permit_id']);
            $table->dropIndex(['project_permit_id']);
            $table->dropColumn('project_permit_id');
        });
    }
};
