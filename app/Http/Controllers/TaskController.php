<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Services\TaskService;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected TaskService $service;

    public function __construct(TaskService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all');
        $tasks = $this->service->getAll($filter);
        return view('tasks.index', compact('tasks', 'filter'));
    }

    public function store(StoreTaskRequest $request)
    {
        $task = $this->service->create($request->validated());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($task, 201);
        }

        return redirect()->route('tasks.index')->with('success', 'Task created.');
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->service->update($task, $request->validated());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($task);
        }

        return redirect()->route('tasks.index')->with('success', 'Task updated.');
    }

    public function destroy(Request $request, Task $task)
    {
        $this->service->delete($task);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Task deleted.']);
        }

        return redirect()->route('tasks.index')->with('success', 'Task deleted.');
    }

    /**
     * Reorder via AJAX. expects JSON: { order: [id1, id2, id3] }
     */
    public function reorder(Request $request)
    {
        $ordered = (array)$request->input('order', []);
        $this->service->reorder($ordered);

        return response()->json(['message' => 'Reordered']);
    }

    /**
     * Toggle completed state quickly via ajax (optional route).
     */
    public function toggle(Request $request, Task $task)
    {
        $completed = (bool)$request->input('completed', false);
        $this->service->toggleComplete($task, $completed);

        return response()->json($task);
    }
}
