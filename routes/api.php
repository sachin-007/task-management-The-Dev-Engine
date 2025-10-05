<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TaskDataTableController;

// REST API Routes
Route::apiResource('tasks', TaskController::class)->parameters([
    'tasks' => 'id'
]);

// Additional API Routes
Route::post('tasks/reorder', [TaskController::class, 'reorder'])->name('api.tasks.reorder');
Route::post('tasks/{id}/toggle', [TaskController::class, 'toggle'])->name('api.tasks.toggle');

// DataTable API Routes
Route::get('tasks/datatable', [TaskDataTableController::class, 'index'])->name('api.tasks.datatable');
