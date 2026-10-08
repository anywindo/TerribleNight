<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('health', fn () => response()->json(['status' => 'ok', 'timestamp' => now()]));

Route::prefix('v1')->group(function () {

    // Auth (Rate limited to 5/min)
    Route::prefix('auth')->middleware('throttle:auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('profile', [AuthController::class, 'profile']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
        });
    });

    // Protected API Routes
    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        



        // Teams
        Route::post('teams/{team}/members', [TeamController::class, 'addMember']);
        Route::delete('teams/{team}/members/{user}', [TeamController::class, 'removeMember']);
        Route::apiResource('teams', TeamController::class);



        // Time Entries

        // Reports

    });
});
