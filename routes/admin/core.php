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

// FIX (BUG-13): Consolidated redundant Route::resource() calls into explicit route groups.
// Previously each resource was defined 3-4 times with different middleware, making it
// hard to maintain and easy to misorder. Now uses explicit routes in groups.

use App\Http\Controllers\ClientController;
use App\Http\Controllers\InstitutionController;
use App\Modules\Proyek\Controllers\Public\DocumentController;
use App\Modules\Proyek\Controllers\Public\ProjectController;
use App\Modules\Proyek\Controllers\Public\TaskController;

// Project Management Routes
Route::name('projects.')->prefix('projects')->whereNumber('project')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->name('index')->middleware('permission:projects.view');
    Route::get('create', [ProjectController::class, 'create'])->name('create')->middleware('permission:projects.create');
    Route::post('/', [ProjectController::class, 'store'])->name('store')->middleware('permission:projects.create');
    Route::get('{project}', [ProjectController::class, 'show'])->name('show')->middleware('permission:projects.view');
    Route::get('{project}/edit', [ProjectController::class, 'edit'])->name('edit')->middleware('permission:projects.edit');
    Route::put('{project}', [ProjectController::class, 'update'])->name('update')->middleware('permission:projects.edit');
    Route::delete('{project}', [ProjectController::class, 'destroy'])->name('destroy')->middleware('permission:projects.delete');
    Route::patch('{project}/status', [ProjectController::class, 'updateStatus'])->name('update-status')->middleware('permission:projects.edit');
});

// Task Management Routes
Route::name('tasks.')->prefix('tasks')->whereNumber('task')->group(function () {
    Route::get('/', [TaskController::class, 'index'])->name('index')->middleware('permission:tasks.view');
    Route::get('create', [TaskController::class, 'create'])->name('create')->middleware('permission:tasks.create');
    Route::post('/', [TaskController::class, 'store'])->name('store')->middleware('permission:tasks.create');
    Route::get('{task}', [TaskController::class, 'show'])->name('show')->middleware('permission:tasks.view');
    Route::get('{task}/edit', [TaskController::class, 'edit'])->name('edit')->middleware('permission:tasks.edit');
    Route::put('{task}', [TaskController::class, 'update'])->name('update')->middleware('permission:tasks.edit');
    Route::delete('{task}', [TaskController::class, 'destroy'])->name('destroy')->middleware('permission:tasks.delete');
    Route::patch('{task}/status', [TaskController::class, 'updateStatus'])->name('update-status')->middleware('permission:tasks.edit');
    Route::patch('{task}/assignment', [TaskController::class, 'updateAssignment'])->name('update-assignment')->middleware('permission:tasks.assign');
});
Route::patch('projects/{project}/tasks/reorder', [TaskController::class, 'reorder'])
    ->middleware('permission:tasks.edit')
    ->name('projects.tasks.reorder');

// Document Management Routes
Route::name('documents.')->prefix('documents')->whereNumber('document')->group(function () {
    Route::get('/', [DocumentController::class, 'index'])->name('index')->middleware('permission:documents.view');
    Route::get('create', [DocumentController::class, 'create'])->name('create')->middleware('permission:documents.upload');
    Route::post('/', [DocumentController::class, 'store'])->name('store')->middleware('permission:documents.upload');
    Route::get('{document}', [DocumentController::class, 'show'])->name('show')->middleware('permission:documents.view');
    Route::get('{document}/edit', [DocumentController::class, 'edit'])->name('edit')->middleware('permission:documents.upload');
    Route::put('{document}', [DocumentController::class, 'update'])->name('update')->middleware('permission:documents.upload');
    Route::delete('{document}', [DocumentController::class, 'destroy'])->name('destroy')->middleware('permission:documents.delete');
    Route::get('{document}/download', [DocumentController::class, 'download'])->name('download')->middleware('permission:documents.view');
    // P6 — toggle visibility ke client vault
    Route::patch('{document}/toggle-vault', [App\Http\Controllers\Admin\DocumentVaultAdminController::class, 'toggleVisibility'])->name('toggle-vault')->middleware('permission:documents.upload');
    Route::patch('{document}/vault-meta', [App\Http\Controllers\Admin\DocumentVaultAdminController::class, 'updateMeta'])->name('vault-meta')->middleware('permission:documents.upload');
});
Route::get('api/tasks-by-project', [DocumentController::class, 'getTasksByProject'])
    ->middleware('permission:documents.view')
    ->name('api.tasks-by-project');

// Institution Management Routes
Route::name('institutions.')->prefix('institutions')->whereNumber('institution')->group(function () {
    Route::get('/', [InstitutionController::class, 'index'])->name('index')->middleware('permission:institutions.view');
    Route::get('create', [InstitutionController::class, 'create'])->name('create')->middleware('permission:institutions.create');
    Route::post('/', [InstitutionController::class, 'store'])->name('store')->middleware('permission:institutions.create');
    Route::get('{institution}', [InstitutionController::class, 'show'])->name('show')->middleware('permission:institutions.view');
    Route::get('{institution}/edit', [InstitutionController::class, 'edit'])->name('edit')->middleware('permission:institutions.edit');
    Route::put('{institution}', [InstitutionController::class, 'update'])->name('update')->middleware('permission:institutions.edit');
    Route::delete('{institution}', [InstitutionController::class, 'destroy'])->name('destroy')->middleware('permission:institutions.delete');
});
Route::get('api/institutions', [InstitutionController::class, 'apiIndex'])
    ->middleware('permission:institutions.view')
    ->name('api.institutions');

// Client Management Routes
Route::name('clients.')->prefix('clients')->whereNumber('client')->group(function () {
    Route::get('/', [ClientController::class, 'index'])->name('index')->middleware('permission:clients.view');
    Route::get('create', [ClientController::class, 'create'])->name('create')->middleware('permission:clients.create');
    Route::post('/', [ClientController::class, 'store'])->name('store')->middleware('permission:clients.create');
    Route::get('{client}', [ClientController::class, 'show'])->name('show')->middleware('permission:clients.view');
    Route::get('{client}/edit', [ClientController::class, 'edit'])->name('edit')->middleware('permission:clients.edit');
    Route::put('{client}', [ClientController::class, 'update'])->name('update')->middleware('permission:clients.edit');
    Route::delete('{client}', [ClientController::class, 'destroy'])->name('destroy')->middleware('permission:clients.delete');
});
Route::get('api/clients', [ClientController::class, 'apiIndex'])
    ->middleware('permission:clients.view')
    ->name('api.clients');
