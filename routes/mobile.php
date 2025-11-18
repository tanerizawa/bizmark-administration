<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mobile\DashboardController;
use App\Http\Controllers\Mobile\ProjectController;
use App\Http\Controllers\Mobile\TaskController;
use App\Http\Controllers\Mobile\ApprovalController;
use App\Http\Controllers\Mobile\FinancialController;
use App\Http\Controllers\Mobile\NotificationController;
use App\Http\Controllers\Mobile\ProfileController;

/*
|--------------------------------------------------------------------------
| Mobile Routes
|--------------------------------------------------------------------------
|
| Routes untuk PWA Mobile Admin dengan prefix /m
| Semua routes menggunakan middleware: auth, mobile
| Optimized untuk touch interface dan mobile bandwidth
|
*/

// Mobile welcome page (tidak perlu auth)
Route::prefix('m')->middleware(['mobile'])->group(function () {
    Route::get('/welcome', function() {
        if (auth()->check()) {
            return redirect()->route('mobile.dashboard');
        }
        return view('mobile.welcome');
    })->name('mobile.welcome');
});

// Protected mobile routes (perlu auth)
Route::prefix('m')->middleware(['auth', 'mobile'])->name('mobile.')->group(function () {
    
    // Dashboard Mobile
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/refresh', [DashboardController::class, 'refresh'])->name('dashboard.refresh');
    
    // Projects
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::get('/search', [ProjectController::class, 'search'])->name('search');
        Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
        Route::post('/{project}/note', [ProjectController::class, 'addNote'])->name('note');
        Route::patch('/{project}/status', [ProjectController::class, 'updateStatus'])->name('status');
        Route::get('/{project}/timeline', [ProjectController::class, 'timeline'])->name('timeline');
    });
    
    // Tasks
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::get('/my', [TaskController::class, 'myTasks'])->name('my');
        Route::get('/urgent', [TaskController::class, 'urgent'])->name('urgent');
        Route::get('/{task}', [TaskController::class, 'show'])->name('show');
        Route::patch('/{task}/complete', [TaskController::class, 'complete'])->name('complete');
        Route::patch('/{task}/status', [TaskController::class, 'updateStatus'])->name('status');
        Route::post('/{task}/comment', [TaskController::class, 'addComment'])->name('comment');
    });
    
    // Approvals (Critical for Mobile)
    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/', [ApprovalController::class, 'index'])->name('index');
        Route::get('/pending', [ApprovalController::class, 'pending'])->name('pending');
        Route::get('/{type}/{id}', [ApprovalController::class, 'show'])->name('show');
        Route::post('/{type}/{id}/approve', [ApprovalController::class, 'approve'])->name('approve');
        Route::post('/{type}/{id}/reject', [ApprovalController::class, 'reject'])->name('reject');
        Route::post('/bulk-approve', [ApprovalController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [ApprovalController::class, 'bulkReject'])->name('bulk-reject');
    });
    
    // Financial
    Route::prefix('financial')->name('financial.')->group(function () {
        Route::get('/', [FinancialController::class, 'index'])->name('index');
        Route::get('/cash-flow', [FinancialController::class, 'cashFlow'])->name('cash-flow');
        Route::get('/receivables', [FinancialController::class, 'receivables'])->name('receivables');
        Route::get('/expenses', [FinancialController::class, 'expenses'])->name('expenses');
        Route::get('/invoices/{invoice}', [FinancialController::class, 'showInvoice'])->name('invoice');
    });
    
    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/read', [NotificationController::class, 'markRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllRead'])->name('read-all');
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
    });
    
    // Profile & Settings
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::patch('/update', [ProfileController::class, 'update'])->name('update');
        Route::post('/avatar', [ProfileController::class, 'uploadAvatar'])->name('avatar');
        Route::post('/preferences', [ProfileController::class, 'updatePreferences'])->name('preferences');
    });
    
    // Quick Actions (untuk bottom sheet)
    Route::post('/quick/project', [ProjectController::class, 'quickCreate'])->name('quick.project');
    Route::post('/quick/task', [TaskController::class, 'quickCreate'])->name('quick.task');
    Route::post('/quick/expense', [FinancialController::class, 'quickExpense'])->name('quick.expense');
    
    // Offline Sync
    Route::post('/sync', [DashboardController::class, 'sync'])->name('sync');
    
    // Force Desktop Mode Toggle
    Route::post('/force-desktop', function () {
        session(['force_desktop' => true]);
        return redirect('/dashboard');
    })->name('force-desktop');
    
    Route::post('/force-mobile', function () {
        session()->forget('force_desktop');
        return redirect()->route('mobile.dashboard');
    })->name('force-mobile');
});
