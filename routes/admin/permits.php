<?php

use Illuminate\Support\Facades\Route;

// Master Data - Permit Types & Templates
Route::middleware('permission:master_data.manage')->group(function () {
    Route::resource('permit-types', App\Modules\Perizinan\Controllers\Public\PermitTypeController::class);
    Route::patch('permit-types/{permitType}/toggle-status', [App\Modules\Perizinan\Controllers\Public\PermitTypeController::class, 'toggleStatus'])->name('permit-types.toggle-status');

    Route::resource('permit-templates', App\Modules\Perizinan\Controllers\Public\PermitTemplateController::class);
    Route::post('permit-templates/{permitTemplate}/apply', [App\Modules\Perizinan\Controllers\Public\PermitTemplateController::class, 'applyToProject'])->name('permit-templates.apply');
});

// Project Permit Management
Route::middleware('permission:projects.view')->group(function () {
    Route::patch('permits/{permit}/status', [App\Modules\Perizinan\Controllers\Public\ProjectPermitController::class, 'updateStatus'])->name('permits.update-status');

    Route::get('projects/{project}/permits', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'index'])->name('projects.permits');
    Route::post('projects/{project}/permits', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'store'])->name('projects.permits.store');
    Route::patch('permits/{permit}', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'update'])->name('permits.update');
    Route::delete('permits/{permit}', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'destroy'])->name('permits.destroy');
    Route::post('projects/{project}/permits/apply-template', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'applyTemplate'])->name('projects.permits.apply-template');
    Route::post('permits/{permit}/dependencies', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'addDependency'])->name('permits.add-dependency');
    Route::post('projects/{project}/permits/reorder', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'reorder'])->name('projects.permits.reorder');
    Route::post('projects/{project}/permits/bulk-update-status', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'bulkUpdateStatus'])->name('projects.permits.bulk-update-status');
    Route::post('projects/{project}/permits/bulk-delete', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'bulkDelete'])->name('projects.permits.bulk-delete');
    Route::post('projects/{project}/permits/{permit}/documents/upload', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'uploadDocument'])->name('permits.documents.upload');
    Route::get('projects/{project}/permits/documents/{document}/download', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'downloadDocument'])->name('permits.documents.download');
    Route::delete('projects/{project}/permits/documents/{document}', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'deleteDocument'])->name('permits.documents.delete');
    Route::post('permits/documents/{document}/delete', [App\Modules\Perizinan\Controllers\Public\PermitController::class, 'deleteDocumentPost'])->name('permits.documents.delete-post');
});
