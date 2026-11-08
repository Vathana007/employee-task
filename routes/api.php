<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ErrorLogController;
use App\Http\Middleware\LoginRateLimit;
use Illuminate\Support\Facades\Route;

// Public Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('api.login')->middleware(LoginRateLimit::class);
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
Route::post('/send-verification-email', [AuthController::class, 'sendVerificationEmail']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    Route::apiResource('users', UserController::class);
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('comments', CommentController::class);
    Route::apiResource('roles', RoleController::class);

    Route::middleware('permission:manage_users')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });

    Route::middleware('permission:manage_projects')->group(function () {
        Route::post('/projects', [ProjectController::class, 'store']);
        Route::put('/projects/{id}', [ProjectController::class, 'update']);
        Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);
    });

    Route::middleware('permission:manage_tasks')->group(function () {
        Route::post('/tasks', [TaskController::class, 'store']);
        Route::put('/tasks/{id}', [TaskController::class, 'update']);
        Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
    });

    Route::middleware('permission:manage_roles')->group(function () {
        Route::apiResource('roles', RoleController::class);
    });

    Route::post('/users/{id}/avatar', [UserController::class, 'uploadAvatar']);
    Route::put('/users/{id}/password', [UserController::class, 'changePassword']);

    Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
        Route::get('/errors', [ErrorLogController::class, 'index']);
        Route::get('/errors/summary', [ErrorLogController::class, 'getSummary']);
        Route::get('/errors/type/{type}', [ErrorLogController::class, 'getByType']);
        Route::get('/errors/user/{userId}', [ErrorLogController::class, 'getByUser']);
        Route::get('/errors/ip/{ip}', [ErrorLogController::class, 'getByIp']);
        Route::post('/errors/{id}/resolve', [ErrorLogController::class, 'resolve']);
    });
});
