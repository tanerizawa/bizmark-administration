<?php

/**
 * Client Portal Routes
 * --------------------
 * Di-include dari routes/web.php dalam group:
 *   Route::prefix('client')->name('client.')->group(function () {
 *       require __DIR__.'/client.php';
 *   });
 *
 * Prefix '/client' dan name prefix 'client.' sudah diterapkan di parent group.
 */

use Illuminate\Support\Facades\Route;

    // Guest routes (login, register, password reset)
    Route::middleware('guest:client')->group(function () {
        // Redirect old client login to unified login
        Route::get('/login', fn() => redirect('/login'))->name('legacy.login');
        
        // Keep legacy POST for backwards compatibility (will be deprecated)
        Route::post('/login', [App\Http\Controllers\Auth\ClientAuthController::class, 'login'])
            ->middleware('throttle:5,1'); // Max 5 attempts per minute
        
        Route::get('/register', [App\Http\Controllers\Auth\ClientAuthController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [App\Http\Controllers\Auth\ClientAuthController::class, 'register'])
            ->middleware('throttle:10,1'); // Max 10 registrations per minute
        
        Route::get('/forgot-password', [App\Http\Controllers\Auth\ClientAuthController::class, 'showForgotPasswordForm'])->name('password.request');
        Route::post('/forgot-password', [App\Http\Controllers\Auth\ClientAuthController::class, 'sendResetLinkEmail'])
            ->middleware('throttle:5,1') // Max 5 attempts per minute
            ->name('password.email');
        
        Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\ClientAuthController::class, 'showResetPasswordForm'])->name('password.reset');
        Route::post('/reset-password', [App\Http\Controllers\Auth\ClientAuthController::class, 'resetPassword'])
            ->middleware('throttle:3,1') // Max 3 attempts per minute
            ->name('password.update');
    });
    
    // Protected routes (requires authentication)
    Route::middleware('auth:client')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [App\Http\Controllers\Auth\ClientAuthController::class, 'logout'])->name('logout');
        
        // Service Catalog Routes (KBLI-based AI System)
        Route::get('/services', [App\Http\Controllers\Client\ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/{kbliCode}/context', [App\Http\Controllers\Client\ServiceController::class, 'context'])->name('services.context');
        Route::post('/services/{kbliCode}/context', [App\Http\Controllers\Client\ServiceController::class, 'storeContext'])->name('services.storeContext');
        Route::get('/services/{kbliCode}/download-summary', [App\Http\Controllers\Client\ServiceController::class, 'downloadSummary'])->name('services.downloadSummary');
        Route::get('/services/{kbliCode}', [App\Http\Controllers\Client\ServiceController::class, 'show'])->name('services.show');
        
        // Application Routes
        Route::get('/applications', [App\Http\Controllers\Client\ApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/create', [App\Http\Controllers\Client\ApplicationController::class, 'create'])->name('applications.create');
        Route::post('/applications', [App\Http\Controllers\Client\ApplicationController::class, 'store'])->name('applications.store');
        Route::post('/applications/select-permits', [App\Http\Controllers\Client\ApplicationController::class, 'selectPermits'])->name('applications.select-permits');
        Route::get('/applications/create-package', [App\Http\Controllers\Client\ApplicationController::class, 'createPackage'])->name('applications.create-package');
        Route::post('/applications/store-package', [App\Http\Controllers\Client\ApplicationController::class, 'storeMultiple'])->name('applications.store-package');
        Route::get('/applications/{id}', [App\Http\Controllers\Client\ApplicationController::class, 'show'])->name('applications.show');
        Route::get('/applications/{id}/edit', [App\Http\Controllers\Client\ApplicationController::class, 'edit'])->name('applications.edit');
        Route::put('/applications/{id}', [App\Http\Controllers\Client\ApplicationController::class, 'update'])->name('applications.update');
        Route::get('/applications/{id}/preview-submit', [App\Http\Controllers\Client\ApplicationController::class, 'previewSubmit'])->name('applications.preview-submit');
        Route::post('/applications/{id}/submit', [App\Http\Controllers\Client\ApplicationController::class, 'submit'])->name('applications.submit');
        Route::post('/applications/{id}/cancel', [App\Http\Controllers\Client\ApplicationController::class, 'cancel'])->name('applications.cancel');
        Route::post('/applications/{id}/documents', [App\Http\Controllers\Client\ApplicationController::class, 'uploadDocument'])->name('applications.documents.upload');
        Route::delete('/applications/{applicationId}/documents/{documentId}', [App\Http\Controllers\Client\ApplicationController::class, 'deleteDocument'])->name('applications.documents.delete');
        
        // Quotation Routes (Phase 3.4)
        Route::get('/applications/{id}/quotation', [App\Http\Controllers\Client\ClientQuotationController::class, 'show'])->name('quotations.show');
        Route::post('/applications/{id}/quotation/accept', [App\Http\Controllers\Client\ClientQuotationController::class, 'accept'])->name('quotations.accept');
        Route::post('/applications/{id}/quotation/reject', [App\Http\Controllers\Client\ClientQuotationController::class, 'reject'])->name('quotations.reject');
        
        // Payment Routes (Phase 4)
        Route::get('/applications/{id}/payment', [App\Http\Controllers\Client\PaymentController::class, 'show'])->name('payments.show');
        Route::post('/applications/{id}/payment/initiate', [App\Http\Controllers\Client\PaymentController::class, 'initiate'])->name('payments.initiate');
        Route::post('/applications/{id}/payment/manual', [App\Http\Controllers\Client\PaymentController::class, 'manual'])->name('payments.manual');
        Route::get('/applications/{id}/payment/{paymentId}/success', [App\Http\Controllers\Client\PaymentController::class, 'success'])->name('payments.success');

        // Notifications
        Route::get('/notifications', [App\Http\Controllers\Client\NotificationController::class, 'index'])
            ->name('notifications.index');
        Route::post('/notifications/read-all', [App\Http\Controllers\Client\NotificationController::class, 'markAllRead'])
            ->name('notifications.read-all');

        // Project Routes
        Route::get('/projects', [App\Http\Controllers\Client\ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{id}', [App\Http\Controllers\Client\ProjectController::class, 'show'])->name('projects.show');
        
        // Document Routes
        Route::get('/documents', [App\Http\Controllers\Client\DocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents', [App\Http\Controllers\Client\DocumentController::class, 'store'])->name('documents.store');
        Route::get('/documents/{id}/download', [App\Http\Controllers\Client\DocumentController::class, 'download'])->name('documents.download');
        
        // Profile Routes
        Route::get('/profile', [App\Http\Controllers\Client\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [App\Http\Controllers\Client\ProfileController::class, 'update'])->name('profile.update');
        
        // Email Verification Routes
        Route::get('/verify-email', [App\Http\Controllers\Auth\ClientAuthController::class, 'showVerifyEmailNotice'])
            ->name('verification.notice');
        Route::post('/email/verification-notification', [App\Http\Controllers\Auth\ClientAuthController::class, 'resendVerificationEmail'])
            ->middleware('throttle:3,1')
            ->name('verification.send');
    });
    
    // Email verification callback (accessible without auth to allow verification)
    Route::get('/verify-email/{id}/{hash}', [App\Http\Controllers\Auth\ClientAuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
