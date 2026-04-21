<?php

use Illuminate\Support\Facades\Route;

// Email routes dipindah ke App\Modules\Email\routes.php (EmailServiceProvider)

// SEO routes dipindah ke App\Modules\ContentSeo\routes.php (ContentSeoServiceProvider)

// AI routes dipindah ke App\Modules\AI\routes.php (AIServiceProvider)

// KBLI Settings
Route::name('admin.')->middleware(['auth', 'permission:master_data.manage'])->group(function () {
    Route::prefix('settings/kbli')->name('settings.kbli.')->group(function () {
        Route::get('/', [App\Modules\Perizinan\Controllers\Admin\KbliSettingsController::class, 'index'])->name('index');
        Route::post('import', [App\Modules\Perizinan\Controllers\Admin\KbliSettingsController::class, 'import'])->name('import');
        Route::get('template', [App\Modules\Perizinan\Controllers\Admin\KbliSettingsController::class, 'downloadTemplate'])->name('template');
        Route::get('export', [App\Modules\Perizinan\Controllers\Admin\KbliSettingsController::class, 'export'])->name('export');
        Route::delete('clear', [App\Modules\Perizinan\Controllers\Admin\KbliSettingsController::class, 'clear'])->name('clear');
    });
});
