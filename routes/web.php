<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Api\TaskDataTableController;

Route::get('/', fn() => redirect()->route('tasks.index'));

Route::resource('tasks', TaskController::class)->only([
    'index', 'store', 'update', 'destroy'
]);

// reorder + toggle (web AJAX)
Route::post('tasks/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');
Route::post('tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');

// API routes for DataTable
Route::get('api/tasks', [TaskDataTableController::class, 'index'])->name('api.tasks.index');
