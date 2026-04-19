<?php

use Illuminate\Support\Facades\Route;

Route::prefix('security')->name('admin.security.')->middleware(['permission:security.view', '2fa'])->group(function () {
    Route::get('webhook-metrics', [App\Http\Controllers\Admin\SecurityDashboardController::class, 'webhookMetrics'])
        ->name('webhook-metrics');
    Route::get('audit-logs', [App\Http\Controllers\Admin\SecurityDashboardController::class, 'auditLogs'])
        ->name('audit-logs');
});

Route::prefix('security/2fa')->name('admin.security.2fa.')->group(function () {
    Route::get('setup', [App\Http\Controllers\Admin\TwoFactorController::class, 'setup'])->name('setup');
    Route::post('enable', [App\Http\Controllers\Admin\TwoFactorController::class, 'enable'])
        ->middleware('throttle:5,15')
        ->name('enable');
    Route::get('challenge', [App\Http\Controllers\Admin\TwoFactorController::class, 'challenge'])->name('challenge');
    Route::post('challenge', [App\Http\Controllers\Admin\TwoFactorController::class, 'verifyChallenge'])
        ->middleware('throttle:5,15')
        ->name('verify');
    Route::post('recovery-codes/regenerate', [App\Http\Controllers\Admin\TwoFactorController::class, 'regenerateRecoveryCodes'])
        ->middleware('2fa')
        ->name('recovery.regenerate');
    Route::post('disable', [App\Http\Controllers\Admin\TwoFactorController::class, 'disable'])
        ->middleware('2fa')
        ->name('disable');
});
