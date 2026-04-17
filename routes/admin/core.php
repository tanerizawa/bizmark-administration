<?php

    // Dashboard - desktop version (mobile auto-redirects handled in DetectMobile middleware)
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    Route::post('/dashboard/clear-cache', [App\Http\Controllers\DashboardController::class, 'clearCache'])->name('dashboard.clear-cache');
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home');

    // Admin Profile Routes
    Route::name('admin.')->group(function () {
        Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::get('/notifications', [App\Http\Controllers\Admin\ProfileController::class, 'notifications'])->name('notifications');
        Route::get('/notifications/{id}/read', [App\Http\Controllers\Admin\ProfileController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [App\Http\Controllers\Admin\ProfileController::class, 'markAllAsRead'])->name('notifications.read-all');
        
        // Global Search
        Route::get('/search', [App\Http\Controllers\Admin\SearchController::class, 'search'])->name('search');
    });

    // Export Routes
    Route::middleware('permission:projects.view')->group(function () {
        Route::get('export/projects', [App\Http\Controllers\ExportController::class, 'exportProjects'])->name('export.projects');
        Route::get('export/projects/{id}/details', [App\Http\Controllers\ExportController::class, 'exportProjectDetails'])->name('export.project.details');
    });

    // Project Management Routes
    Route::middleware('permission:projects.view')->group(function () {
        Route::resource('projects', App\Http\Controllers\ProjectController::class);
        Route::patch('projects/{project}/status', [App\Http\Controllers\ProjectController::class, 'updateStatus'])->name('projects.update-status');
    });

    // Task Management Routes
    Route::middleware('permission:tasks.view')->group(function () {
        Route::resource('tasks', App\Http\Controllers\TaskController::class);
        Route::patch('tasks/{task}/status', [App\Http\Controllers\TaskController::class, 'updateStatus'])->name('tasks.update-status');
        Route::patch('tasks/{task}/assignment', [App\Http\Controllers\TaskController::class, 'updateAssignment'])->name('tasks.update-assignment');
        Route::patch('projects/{project}/tasks/reorder', [App\Http\Controllers\TaskController::class, 'reorder'])->name('projects.tasks.reorder');
    });

    // Document Management Routes
    Route::middleware('permission:documents.view')->group(function () {
        Route::resource('documents', App\Http\Controllers\DocumentController::class);
        Route::get('documents/{document}/download', [App\Http\Controllers\DocumentController::class, 'download'])->name('documents.download');
        Route::get('api/tasks-by-project', [App\Http\Controllers\DocumentController::class, 'getTasksByProject'])->name('api.tasks-by-project');
    });

    // Institution Management Routes
    Route::middleware('permission:institutions.view')->group(function () {
        Route::resource('institutions', App\Http\Controllers\InstitutionController::class);
        Route::get('api/institutions', [App\Http\Controllers\InstitutionController::class, 'apiIndex'])->name('api.institutions');
    });

    // Client Management Routes
    Route::middleware('permission:clients.view')->group(function () {
        Route::resource('clients', App\Http\Controllers\ClientController::class);
        Route::get('api/clients', [App\Http\Controllers\ClientController::class, 'apiIndex'])->name('api.clients');
    });
