<?php

use App\Http\Controllers\Api\KbliController;
use App\Http\Controllers\Api\KbliRecommendationController;
use App\Http\Controllers\Api\ConsultationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// KBLI Search & Autocomplete (Public with rate limiting)
Route::prefix('kbli')->middleware('throttle:60,1')->group(function () {
    Route::get('search', [KbliController::class, 'search']);
    Route::get('popular', [KbliController::class, 'popular']);
    Route::get('{code}', [KbliController::class, 'show']);
});

// Free Consultation (Public with stricter rate limiting)
Route::prefix('consultation')->middleware('throttle:10,1')->group(function () {
    Route::post('submit', [ConsultationController::class, 'submit']);
    Route::post('quick-estimate', [ConsultationController::class, 'quickEstimate'])->middleware('throttle:30,1');
});

// KBLI AI Recommendations
Route::prefix('kbli-recommendations')->group(function () {
    Route::post('/', [KbliRecommendationController::class, 'getRecommendations']);
    Route::post('refresh', [KbliRecommendationController::class, 'refresh'])->middleware('auth:sanctum');
    Route::get('stats', [KbliRecommendationController::class, 'stats'])->middleware('auth:sanctum');
});

// Shapefile/Polygon Tool (Public with rate limiting + session for client auth)
Route::prefix('shapefile')->middleware(['throttle:10,1', \Illuminate\Session\Middleware\StartSession::class])->group(function () {
    Route::post('generate', [App\Http\Controllers\Api\ShapefileApiController::class, 'generate']);
    Route::post('calculate', [App\Http\Controllers\Api\ShapefileApiController::class, 'calculate'])->middleware('throttle:30,1');
    Route::post('check-email', [App\Http\Controllers\Api\ShapefileApiController::class, 'checkEmail'])->middleware('throttle:15,1');
    Route::get('{projectId}/pdf', [App\Http\Controllers\Api\ShapefileApiController::class, 'generatePdf'])->middleware('throttle:5,1');
    Route::post('{projectId}/rtrw', [App\Http\Controllers\Api\ShapefileApiController::class, 'storeRtrwAnalysis'])->middleware('throttle:10,1');
});

// RTRW Spatial Zoning (GISTARU proxy)
Route::prefix('rtrw')->middleware('throttle:30,1')->group(function () {
    Route::get('provinces', [App\Http\Controllers\Api\RtrwProxyController::class, 'provinces']);
    Route::get('zona', [App\Http\Controllers\Api\RtrwProxyController::class, 'zona'])->middleware('throttle:20,1');
    Route::post('analyze', [App\Http\Controllers\Api\RtrwProxyController::class, 'analyze'])->middleware('throttle:20,1');
    Route::get('layers/{provinceCode}', [App\Http\Controllers\Api\RtrwProxyController::class, 'layers']);
    Route::get('legend/{provinceCode}', [App\Http\Controllers\Api\RtrwProxyController::class, 'legend']);
    Route::get('map-export/{provinceCode}', [App\Http\Controllers\Api\RtrwProxyController::class, 'mapExport'])->middleware('throttle:60,1');
});
