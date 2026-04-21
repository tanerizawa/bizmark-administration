<?php

use Illuminate\Support\Facades\Route;
use App\Modules\AI\Controllers\Admin\DocumentAIController;

Route::middleware(['web', 'auth', 'permission:documents.view'])->group(function () {
    Route::prefix('admin/projects/{project}/ai')->name('ai.')->group(function () {
        Route::get('paraphrase', [DocumentAIController::class, 'create'])->name('paraphrase.create');
        Route::post('paraphrase', [DocumentAIController::class, 'store'])->name('paraphrase.store');

        Route::get('drafts', [DocumentAIController::class, 'index'])->name('drafts.index');
        Route::get('drafts/{draft}', [DocumentAIController::class, 'show'])->name('drafts.show');
        Route::put('drafts/{draft}', [DocumentAIController::class, 'update'])->name('drafts.update');

        Route::post('drafts/{draft}/approve', [DocumentAIController::class, 'approve'])->name('drafts.approve');
        Route::post('drafts/{draft}/reject', [DocumentAIController::class, 'reject'])->name('drafts.reject');
        Route::delete('drafts/{draft}', [DocumentAIController::class, 'destroy'])->name('drafts.destroy');
        Route::get('drafts/{draft}/export', [DocumentAIController::class, 'export'])->name('drafts.export');

        Route::post('drafts/{draft}/check-compliance', [DocumentAIController::class, 'checkCompliance'])->name('drafts.check-compliance');
        Route::get('drafts/{draft}/compliance-results', [DocumentAIController::class, 'getComplianceResults'])->name('drafts.compliance-results');
        Route::get('drafts/{draft}/compliance-report', [DocumentAIController::class, 'exportComplianceReport'])->name('drafts.compliance-report');

        Route::get('status', [DocumentAIController::class, 'status'])->name('status');
    });

    Route::post('admin/ai/templates/upload', [DocumentAIController::class, 'uploadTemplate'])
        ->middleware('permission:documents.manage')
        ->name('ai.templates.upload');
});
