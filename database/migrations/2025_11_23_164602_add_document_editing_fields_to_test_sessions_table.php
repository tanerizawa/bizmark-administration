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
        Schema::table('test_sessions', function (Blueprint $table) {
            // Document submission fields
            $table->string('submitted_file_path', 500)->nullable()->after('time_taken_minutes')->comment('Path to submitted document');
            $table->timestamp('submitted_at')->nullable()->after('submitted_file_path')->comment('When candidate uploaded the file');
            
            // Evaluation fields
            $table->json('evaluation_scores')->nullable()->after('submitted_at')->comment('Detailed scores per criteria');
            $table->foreignId('evaluator_id')->nullable()->after('evaluation_scores')->constrained('users')->nullOnDelete()->comment('User who evaluated');
            $table->text('evaluator_notes')->nullable()->after('evaluator_id')->comment('Notes from evaluator');
            $table->timestamp('evaluated_at')->nullable()->after('evaluator_notes')->comment('When evaluation completed');
            $table->boolean('requires_manual_review')->default(false)->after('evaluated_at')->comment('For document-editing type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_sessions', function (Blueprint $table) {
            $table->dropForeign(['evaluator_id']);
            $table->dropColumn([
                'submitted_file_path',
                'submitted_at',
                'evaluation_scores',
                'evaluator_id',
                'evaluator_notes',
                'evaluated_at',
                'requires_manual_review'
            ]);
        });
    }
};
