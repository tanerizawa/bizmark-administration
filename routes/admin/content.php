<?php

use Illuminate\Support\Facades\Route;

// Article Management Routes
// NOTE: create/store MUST be registered before show to prevent the wildcard
// {article} route from capturing "create" as an article slug (Laravel matches
// routes in registration order when literal vs parameter segments conflict).
Route::resource('articles', App\Http\Controllers\ArticleController::class)
    ->only(['index'])
    ->middleware('permission:content.view_articles');
Route::resource('articles', App\Http\Controllers\ArticleController::class)
    ->only(['create', 'store'])
    ->middleware('permission:content.create_articles');
Route::resource('articles', App\Http\Controllers\ArticleController::class)
    ->only(['show'])
    ->middleware('permission:content.view_articles');
Route::resource('articles', App\Http\Controllers\ArticleController::class)
    ->only(['edit', 'update'])
    ->middleware('permission:content.edit_articles');
Route::resource('articles', App\Http\Controllers\ArticleController::class)
    ->only(['destroy'])
    ->middleware('permission:content.delete_articles');
Route::post('articles/{article}/publish', [App\Http\Controllers\ArticleController::class, 'publish'])
    ->middleware('permission:content.publish_articles')
    ->name('articles.publish');
Route::post('articles/{article}/unpublish', [App\Http\Controllers\ArticleController::class, 'unpublish'])
    ->middleware('permission:content.publish_articles')
    ->name('articles.unpublish');
Route::post('articles/{article}/archive', [App\Http\Controllers\ArticleController::class, 'archive'])
    ->middleware('permission:content.edit_articles')
    ->name('articles.archive');
Route::post('articles/upload-image', [App\Http\Controllers\ArticleController::class, 'uploadImage'])
    ->middleware('permission:content.edit_articles')
    ->name('articles.upload-image');

// Pexels API Routes
Route::prefix('pexels')->name('pexels.')->middleware('permission:content.manage')->group(function () {
    Route::get('search', [App\Http\Controllers\Admin\PexelsController::class, 'search'])->name('search');
    Route::get('curated', [App\Http\Controllers\Admin\PexelsController::class, 'curated'])->name('curated');
    Route::post('download', [App\Http\Controllers\Admin\PexelsController::class, 'download'])->name('download');
});

// Auto-Post Management Routes
Route::prefix('auto-post')->name('auto-post.')->middleware('permission:content.manage')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\AutoPostController::class, 'index'])->name('index');

    Route::put('config', [App\Http\Controllers\Admin\AutoPostConfigController::class, 'update'])->name('config.update');
    Route::post('config/toggle', [App\Http\Controllers\Admin\AutoPostConfigController::class, 'toggle'])->name('config.toggle');
    Route::get('config', fn () => redirect()->route('auto-post.index', ['tab' => 'config']))->name('config');

    Route::resource('topics', App\Http\Controllers\Admin\ArticleTopicController::class);
    Route::post('topics/bulk-action', [App\Http\Controllers\Admin\ArticleTopicController::class, 'bulkAction'])->name('topics.bulk-action');

    Route::resource('schedules', App\Http\Controllers\Admin\AutoPostScheduleController::class)->except(['edit', 'update']);
    Route::post('schedules/bulk-action', [App\Http\Controllers\Admin\AutoPostScheduleController::class, 'bulkAction'])->name('schedules.bulk-action');
    Route::post('schedules/generate-batch', [App\Http\Controllers\Admin\AutoPostScheduleController::class, 'generateBatch'])->name('schedules.generate-batch');
    Route::post('schedules/{schedule}/retry', [App\Http\Controllers\Admin\AutoPostScheduleController::class, 'retry'])->name('schedules.retry');
    Route::post('schedules/{schedule}/process-now', [App\Http\Controllers\Admin\AutoPostScheduleController::class, 'processNow'])->name('schedules.process-now');

    Route::get('analytics', fn () => redirect()->route('auto-post.index', ['tab' => 'analytics']))->name('analytics');
    Route::get('logs', [App\Http\Controllers\Admin\AutoPostLogController::class, 'index'])->name('logs.index');
    Route::get('logs/recent', [App\Http\Controllers\Admin\AutoPostLogController::class, 'recent'])->name('logs.recent');
});

// ── Service Data Management ──────────────────────────────────────────────────
// Routes for managing services_data.json via admin panel.
// NOTE: Literal paths (create, sub) must come BEFORE wildcard {slug} routes.
Route::prefix('services')->name('admin.services.')->middleware('permission:content.manage')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\ServiceDataController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Admin\ServiceDataController::class, 'create'])->name('create');
    Route::post('/ai-generate', [App\Http\Controllers\Admin\ServiceDataController::class, 'aiGenerate'])->name('ai-generate');
    Route::post('/', [App\Http\Controllers\Admin\ServiceDataController::class, 'store'])->name('store');
    Route::get('/{slug}/edit', [App\Http\Controllers\Admin\ServiceDataController::class, 'edit'])->name('edit');
    Route::put('/{slug}', [App\Http\Controllers\Admin\ServiceDataController::class, 'update'])->name('update');
    Route::delete('/{slug}', [App\Http\Controllers\Admin\ServiceDataController::class, 'destroy'])->name('destroy');

    // Sub-services
    Route::prefix('/{slug}/sub')->name('sub.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ServiceDataController::class, 'subIndex'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\ServiceDataController::class, 'subCreate'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\ServiceDataController::class, 'subStore'])->name('store');
        Route::get('/{subSlug}/edit', [App\Http\Controllers\Admin\ServiceDataController::class, 'subEdit'])->name('edit');
        Route::put('/{subSlug}', [App\Http\Controllers\Admin\ServiceDataController::class, 'subUpdate'])->name('update');
        Route::delete('/{subSlug}', [App\Http\Controllers\Admin\ServiceDataController::class, 'subDestroy'])->name('destroy');
    });
});
