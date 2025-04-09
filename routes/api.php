<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// Public routes (e.g., user registration, login)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (Require Authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']); // List tasks
    Route::post('/tasks/create', [TaskController::class, 'store']); // Create task
    Route::put('/tasks/update/{task_id}', [TaskController::class, 'update']); // Update task
    Route::delete('/tasks/delete/{task_id}', [TaskController::class, 'destroy']); // Delete task
    Route::put('/tasks/{task_id}/restore', [TaskController::class, 'restore']); // Restore deleted task

    Route::get('/categories', [CategoryController::class, 'index']);    // list categories
    Route::post('/categories/create', [CategoryController::class, 'store']);   // create category
    Route::put('/categories/update/{category_id}', [CategoryController::class, 'update']); // update category
    Route::delete('/categories/delete/{category_id}', [CategoryController::class, 'destroy']); // delete category
});
