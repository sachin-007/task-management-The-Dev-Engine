<?php

use App\Models\Task;

/**
 * Get next order value for tasks.
 *
 * @return int
 */
if (!function_exists('task_next_order')) {
    function task_next_order(): int
    {
        return (int)((Task::max('order') ?? 0) + 1);
    }
}

if (!function_exists('task_completed_label')) {
    function task_completed_label(bool $completed): string
    {
        return $completed ? 'Completed' : 'Pending';
    }
}
