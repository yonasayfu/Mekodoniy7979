<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserSummaryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/profile', [AuthController::class, 'profile']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/users/summary', [UserSummaryController::class, 'index']);
        Route::apiResource('users', UserController::class)->names([
            'index' => 'api.users.index',
            'store' => 'api.users.store',
            'show' => 'api.users.show',
            'update' => 'api.users.update',
            'destroy' => 'api.users.destroy',
        ]);
        Route::apiResource('roles', RoleController::class)->names([
            'index' => 'api.roles.index',
            'store' => 'api.roles.store',
            'show' => 'api.roles.show',
            'update' => 'api.roles.update',
            'destroy' => 'api.roles.destroy',
        ]);
        Route::apiResource('staff', StaffController::class)->names([
            'index' => 'api.staff.index',
            'store' => 'api.staff.store',
            'show' => 'api.staff.show',
            'update' => 'api.staff.update',
            'destroy' => 'api.staff.destroy',
        ]);
    });
});
