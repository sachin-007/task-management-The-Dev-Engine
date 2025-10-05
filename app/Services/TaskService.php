<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Collection;

class TaskService
{
    public function getAll(string $filter = 'all')
    {
        // $query = Task::orderBy('order');
        // if want last inserted 1st
        $query = Task::orderBy('created_at', 'desc')->orderBy('order');

        if ($filter === 'completed') {
            $query->completed();
        } elseif ($filter === 'incomplete') {
            $query->incomplete();
        }
        return $query->get();
    }

    public function create(array $data): Task
    {
        if (!isset($data['order'])) {
            $data['order'] = task_next_order();
        }
        
        return Task::create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task;
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }

    public function reorder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            // Use upsert if necessary; simple update is fine
            Task::where('id', (int)$id)->update(['order' => $index + 1]);
        }
    }

    public function toggleComplete(Task $task, bool $completed): Task
    {
        $task->completed = $completed;
        $task->save();
        return $task;
    }
}
