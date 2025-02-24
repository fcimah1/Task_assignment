<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// Public routes (e.g., user registration, login)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (Require Authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']); // List tasks
    Route::post('/tasks', [TaskController::class, 'store']); // Create task
    Route::put('/tasks/{task_id}', [TaskController::class, 'update']); // Update task
    Route::delete('/tasks/{task_id}', [TaskController::class, 'destroy']); // Delete task
    
    Route::post('/logout', [AuthController::class, 'logout']); // Logout
});
