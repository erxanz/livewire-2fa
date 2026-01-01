<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PermissionController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API Version 1
Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    // Protected routes
    Route::middleware(['auth:sanctum', 'active'])->group(function () {
        // Auth
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);

        // Users
        Route::apiResource('users', UserController::class)->middleware('permission:view-users');
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->middleware('permission:edit-users');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->middleware('permission:edit-users');

        // Roles
        Route::apiResource('roles', RoleController::class)->middleware('permission:view-roles');
        Route::get('/roles/{role}/permissions', [RoleController::class, 'permissions'])->middleware('permission:view-roles');
        Route::post('/roles/{role}/permissions', [RoleController::class, 'syncPermissions'])->middleware('permission:manage-role-permissions');

        // Permissions
        Route::apiResource('permissions', PermissionController::class)->only(['index', 'show'])->middleware('permission:view-permissions');
    });
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ]);
});
