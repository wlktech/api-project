<?php
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\System\FeatureController;
use App\Http\Controllers\Api\V1\System\ModuleController;
use App\Http\Controllers\Api\V1\System\PermissionController;
use App\Http\Controllers\Api\V1\System\RoleController;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:api')->prefix('system')->group(function () {
    Route::resource('modules', ModuleController::class);
    Route::resource('features', FeatureController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
});