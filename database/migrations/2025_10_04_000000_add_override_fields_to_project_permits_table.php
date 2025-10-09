<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('project_permits', function (Blueprint $table) {
            $table->boolean('override_dependencies')->default(false)->after('notes');
            $table->text('override_reason')->nullable()->after('override_dependencies');
            $table->foreignId('override_by_user_id')->nullable()->after('override_reason')->constrained('users')->nullOnDelete();
            $table->timestamp('override_at')->nullable()->after('override_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_permits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('override_by_user_id');
            $table->dropColumn([
                'override_dependencies',
                'override_reason',
                'override_at',
            ]);
        });
    }
};
