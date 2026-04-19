<?php

use Illuminate\Support\Facades\Route;

    // Dashboard - desktop version (mobile auto-redirects handled in DetectMobile middleware)
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    Route::post('/dashboard/clear-cache', [App\Http\Controllers\DashboardController::class, 'clearCache'])
        ->middleware('permission:settings.manage')
        ->name('dashboard.clear-cache');
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
    Route::resource('projects', App\Http\Controllers\ProjectController::class)
        ->only(['index', 'show'])
        ->whereNumber('project')
        ->middleware('permission:projects.view');
    Route::resource('projects', App\Http\Controllers\ProjectController::class)
        ->only(['create', 'store'])
        ->middleware('permission:projects.create');
    Route::resource('projects', App\Http\Controllers\ProjectController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:projects.edit');
    Route::resource('projects', App\Http\Controllers\ProjectController::class)
        ->only(['destroy'])
        ->middleware('permission:projects.delete');
    Route::patch('projects/{project}/status', [App\Http\Controllers\ProjectController::class, 'updateStatus'])
        ->middleware('permission:projects.edit')
        ->name('projects.update-status');

    // Task Management Routes
    Route::resource('tasks', App\Http\Controllers\TaskController::class)
        ->only(['index', 'show'])
        ->whereNumber('task')
        ->middleware('permission:tasks.view');
    Route::resource('tasks', App\Http\Controllers\TaskController::class)
        ->only(['create', 'store'])
        ->middleware('permission:tasks.create');
    Route::resource('tasks', App\Http\Controllers\TaskController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:tasks.edit');
    Route::resource('tasks', App\Http\Controllers\TaskController::class)
        ->only(['destroy'])
        ->middleware('permission:tasks.delete');
    Route::patch('tasks/{task}/status', [App\Http\Controllers\TaskController::class, 'updateStatus'])
        ->middleware('permission:tasks.edit')
        ->name('tasks.update-status');
    Route::patch('tasks/{task}/assignment', [App\Http\Controllers\TaskController::class, 'updateAssignment'])
        ->middleware('permission:tasks.assign')
        ->name('tasks.update-assignment');
    Route::patch('projects/{project}/tasks/reorder', [App\Http\Controllers\TaskController::class, 'reorder'])
        ->middleware('permission:tasks.edit')
        ->name('projects.tasks.reorder');

    // Document Management Routes
    Route::resource('documents', App\Http\Controllers\DocumentController::class)
        ->only(['index', 'show'])
        ->whereNumber('document')
        ->middleware('permission:documents.view');
    Route::resource('documents', App\Http\Controllers\DocumentController::class)
        ->only(['create', 'store', 'edit', 'update'])
        ->middleware('permission:documents.upload');
    Route::resource('documents', App\Http\Controllers\DocumentController::class)
        ->only(['destroy'])
        ->middleware('permission:documents.delete');
    Route::get('documents/{document}/download', [App\Http\Controllers\DocumentController::class, 'download'])
        ->middleware('permission:documents.view')
        ->name('documents.download');
    Route::get('api/tasks-by-project', [App\Http\Controllers\DocumentController::class, 'getTasksByProject'])
        ->middleware('permission:documents.view')
        ->name('api.tasks-by-project');

    // Institution Management Routes
    Route::resource('institutions', App\Http\Controllers\InstitutionController::class)
        ->only(['index', 'show'])
        ->whereNumber('institution')
        ->middleware('permission:institutions.view');
    Route::resource('institutions', App\Http\Controllers\InstitutionController::class)
        ->only(['create', 'store'])
        ->middleware('permission:institutions.create');
    Route::resource('institutions', App\Http\Controllers\InstitutionController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:institutions.edit');
    Route::resource('institutions', App\Http\Controllers\InstitutionController::class)
        ->only(['destroy'])
        ->middleware('permission:institutions.delete');
    Route::get('api/institutions', [App\Http\Controllers\InstitutionController::class, 'apiIndex'])
        ->middleware('permission:institutions.view')
        ->name('api.institutions');

    // Client Management Routes
    Route::resource('clients', App\Http\Controllers\ClientController::class)
        ->only(['index', 'show'])
        ->whereNumber('client')
        ->middleware('permission:clients.view');
    Route::resource('clients', App\Http\Controllers\ClientController::class)
        ->only(['create', 'store'])
        ->middleware('permission:clients.create');
    Route::resource('clients', App\Http\Controllers\ClientController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:clients.edit');
    Route::resource('clients', App\Http\Controllers\ClientController::class)
        ->only(['destroy'])
        ->middleware('permission:clients.delete');
    Route::get('api/clients', [App\Http\Controllers\ClientController::class, 'apiIndex'])
        ->middleware('permission:clients.view')
        ->name('api.clients');
