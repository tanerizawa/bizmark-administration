<?php

use App\Http\Controllers\Api\KbliController;
use App\Http\Controllers\Api\KbliRecommendationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// KBLI Search (existing)
Route::prefix('kbli')->group(function () {
    Route::get('search', [KbliController::class, 'search']);
    Route::get('{code}', [KbliController::class, 'show']);
});

// KBLI AI Recommendations
Route::prefix('kbli-recommendations')->group(function () {
    Route::post('/', [KbliRecommendationController::class, 'getRecommendations']);
    Route::post('refresh', [KbliRecommendationController::class, 'refresh'])->middleware('auth:sanctum');
    Route::get('stats', [KbliRecommendationController::class, 'stats'])->middleware('auth:sanctum');
});
