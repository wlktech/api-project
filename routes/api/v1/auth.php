<?php
use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;



Route::prefix('auth')->group(function () {
    // Public routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']); // Using refresh token
    
    // Protected routes
    Route::middleware('auth:api')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout-all', [AuthController::class, 'logoutAll']);
        Route::post('refresh-access-token', [AuthController::class, 'refreshAccessToken']); // Using JWT refresh
        Route::get('sessions', [AuthController::class, 'sessions']);
        Route::delete('sessions/{sessionId}', [AuthController::class, 'revokeSession']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
    });
});