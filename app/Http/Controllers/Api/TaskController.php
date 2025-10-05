<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Http\Traits\ApiResponse;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    use ApiResponse;

    protected TaskService $service;

    public function __construct(TaskService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of tasks
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filter = $request->query('filter', 'all');
            $tasks = $this->service->getAll($filter);
            
            return $this->successResponse(
                TaskResource::collection($tasks),
                'Tasks retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve tasks: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created task
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        try {
            $task = $this->service->create($request->validated());
            
            return $this->createdResponse(
                new TaskResource($task),
                'Task created successfully'
            );
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create task: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified task
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $task = Task::findOrFail($id);
            
            return $this->successResponse(
                new TaskResource($task),
                'Task retrieved successfully'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Task not found');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve task: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified task
     */
    public function update(UpdateTaskRequest $request, $id): JsonResponse
    {
        try {
            $task = Task::findOrFail($id);
            $this->service->update($task, $request->validated());
            
            return $this->updatedResponse(
                new TaskResource($task->fresh()),
                'Task updated successfully'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Task not found');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update task: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified task
     */
    public function destroy($id): JsonResponse
    {
        try {
            $task = Task::findOrFail($id);
            $this->service->delete($task);
            
            return $this->deletedResponse('Task deleted successfully');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Task not found');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete task: ' . $e->getMessage());
        }
    }

    /**
     * Reorder tasks
     */
    public function reorder(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'order' => 'required|array',
                'order.*' => 'integer|exists:tasks,id'
            ]);

            $ordered = (array)$request->input('order', []);
            $this->service->reorder($ordered);
            
            return $this->successResponse(
                null,
                'Tasks reordered successfully'
            );
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to reorder tasks: ' . $e->getMessage());
        }
    }

    /**
     * Toggle task completion status
     */
    public function toggle(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'completed' => 'required|boolean'
            ]);

            $task = Task::findOrFail($id);
            $completed = (bool)$request->input('completed', false);
            $this->service->toggleComplete($task, $completed);
            
            return $this->updatedResponse(
                new TaskResource($task->fresh()),
                'Task status updated successfully'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Task not found');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to toggle task status: ' . $e->getMessage());
        }
    }
}
