<?php

use Illuminate\Support\Facades\Route;

// Settings Management Routes (Phase 2A - Sprint 9)
Route::middleware(['permission:settings.manage', '2fa'])->group(function () {
    Route::get('settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings/general', [App\Http\Controllers\SettingsController::class, 'updateGeneral'])->name('settings.general.update');

    // User Management
    Route::post('settings/users', [App\Http\Controllers\SettingsController::class, 'storeUser'])->name('settings.users.store');
    Route::put('settings/users/{user}', [App\Http\Controllers\SettingsController::class, 'updateUser'])->name('settings.users.update');
    Route::delete('settings/users/{user}', [App\Http\Controllers\SettingsController::class, 'deleteUser'])->name('settings.users.delete');
    Route::patch('settings/users/{user}/toggle-status', [App\Http\Controllers\SettingsController::class, 'toggleUserStatus'])->name('settings.users.toggle-status');

    // Role Management
    Route::post('settings/roles', [App\Http\Controllers\SettingsController::class, 'storeRole'])->name('settings.roles.store');
    Route::put('settings/roles/{role}', [App\Http\Controllers\SettingsController::class, 'updateRole'])->name('settings.roles.update');
    Route::delete('settings/roles/{role}', [App\Http\Controllers\SettingsController::class, 'deleteRole'])->name('settings.roles.delete');

    // Financial Settings
    Route::post('settings/financial/expense-categories', [App\Http\Controllers\SettingsController::class, 'storeExpenseCategory'])->name('settings.financial.expense-categories.store');
    Route::put('settings/financial/expense-categories/{expenseCategory}', [App\Http\Controllers\SettingsController::class, 'updateExpenseCategory'])->name('settings.financial.expense-categories.update');
    Route::delete('settings/financial/expense-categories/{expenseCategory}', [App\Http\Controllers\SettingsController::class, 'deleteExpenseCategory'])->name('settings.financial.expense-categories.delete');

    Route::post('settings/financial/payment-methods', [App\Http\Controllers\SettingsController::class, 'storePaymentMethod'])->name('settings.financial.payment-methods.store');
    Route::put('settings/financial/payment-methods/{paymentMethod}', [App\Http\Controllers\SettingsController::class, 'updatePaymentMethod'])->name('settings.financial.payment-methods.update');
    Route::delete('settings/financial/payment-methods/{paymentMethod}', [App\Http\Controllers\SettingsController::class, 'deletePaymentMethod'])->name('settings.financial.payment-methods.delete');

    Route::post('settings/financial/tax-rates', [App\Http\Controllers\SettingsController::class, 'storeTaxRate'])->name('settings.financial.tax-rates.store');
    Route::put('settings/financial/tax-rates/{taxRate}', [App\Http\Controllers\SettingsController::class, 'updateTaxRate'])->name('settings.financial.tax-rates.update');
    Route::delete('settings/financial/tax-rates/{taxRate}', [App\Http\Controllers\SettingsController::class, 'deleteTaxRate'])->name('settings.financial.tax-rates.delete');

    // Project Settings
    Route::post('settings/project/statuses', [App\Http\Controllers\SettingsController::class, 'storeProjectStatus'])->name('settings.project.statuses.store');
    Route::put('settings/project/statuses/{projectStatus}', [App\Http\Controllers\SettingsController::class, 'updateProjectStatus'])->name('settings.project.statuses.update');
    Route::delete('settings/project/statuses/{projectStatus}', [App\Http\Controllers\SettingsController::class, 'deleteProjectStatus'])->name('settings.project.statuses.delete');

    // Security Settings
    Route::put('settings/security', [App\Http\Controllers\SettingsController::class, 'updateSecurity'])->name('settings.security.update');
});

// Career Management Routes (Admin)
Route::name('admin.')->middleware('permission:recruitment.view')->group(function () {
    // Job Vacancy Management
    Route::resource('jobs', App\Modules\HRM\Controllers\Admin\JobVacancyController::class)->only(['index', 'show']);
    Route::resource('jobs', App\Modules\HRM\Controllers\Admin\JobVacancyController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->middleware('permission:recruitment.manage_jobs');

    // Tab Views for Job Detail Hub
    Route::get('jobs/{id}/applications', [App\Modules\HRM\Controllers\Admin\JobVacancyController::class, 'applications'])->name('jobs.applications');
    Route::get('jobs/{id}/pipeline', [App\Modules\HRM\Controllers\Admin\RecruitmentPipelineController::class, 'jobPipeline'])->name('jobs.pipeline');
    Route::get('jobs/{id}/tests', [App\Modules\HRM\Controllers\Admin\JobVacancyController::class, 'tests'])->name('jobs.tests');
    Route::get('jobs/{id}/interviews', [App\Modules\HRM\Controllers\Admin\InterviewScheduleController::class, 'jobInterviews'])->name('jobs.interviews');

    // Job Application Management
    Route::get('applications', [App\Modules\HRM\Controllers\Admin\JobApplicationController::class, 'index'])->name('applications.index');
    Route::get('applications/{id}', [App\Modules\HRM\Controllers\Admin\JobApplicationController::class, 'show'])->name('applications.show');
    Route::patch('applications/{id}/status', [App\Modules\HRM\Controllers\Admin\JobApplicationController::class, 'updateStatus'])
        ->middleware('permission:recruitment.process_applications')
        ->name('applications.update-status');
    Route::get('applications/{id}/download-cv', [App\Modules\HRM\Controllers\Admin\JobApplicationController::class, 'downloadCv'])->name('applications.download-cv');
    Route::get('applications/{id}/download-portfolio', [App\Modules\HRM\Controllers\Admin\JobApplicationController::class, 'downloadPortfolio'])->name('applications.download-portfolio');
    Route::delete('applications/{id}', [App\Modules\HRM\Controllers\Admin\JobApplicationController::class, 'destroy'])
        ->middleware('permission:recruitment.process_applications')
        ->name('applications.destroy');
});
