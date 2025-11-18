<?php

use App\Http\Controllers\Api\KbliController;
use App\Http\Controllers\Api\KbliRecommendationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Health Check / Ping endpoint for PWA
Route::head('ping', function () {
    return response()->json(['status' => 'ok'], 200);
});

Route::get('ping', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'server_time' => now()->format('Y-m-d H:i:s')
    ], 200);
});

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
