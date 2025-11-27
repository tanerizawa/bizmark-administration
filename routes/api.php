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
