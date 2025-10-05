<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class TaskDataTableController extends Controller
{
    use ApiResponse;

    /**
     * Get data for DataTable with server-side processing
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Task::query();
            
            // Apply filter
            $filter = $request->query('filter', 'all');
            if ($filter === 'completed') {
                $query->completed();
            } elseif ($filter === 'incomplete') {
                $query->incomplete();
            }
            
            // Check if this is a DataTable request or scroll view request
            if ($request->has('draw')) {
                return $this->handleDataTableRequest($request, $query);
            } else {
                return $this->handleScrollViewRequest($request, $query);
            }
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve tasks: ' . $e->getMessage());
        }
    }

    /**
     * Handle DataTable server-side processing request
     */
    private function handleDataTableRequest(Request $request, $query): JsonResponse
    {
        try {
            $start = $request->get('start', 0);
            $length = $request->get('length', 10);
            $searchValue = $request->get('search')['value'] ?? '';
            $orderColumn = $request->get('order')[0]['column'] ?? 0;
            $orderDir = $request->get('order')[0]['dir'] ?? 'asc';
            
            // Define column mapping
            $columns = ['order', 'completed', 'title', 'description', 'id'];
            $orderColumnName = $columns[$orderColumn] ?? 'order';
            
            // Apply search
            if (!empty($searchValue)) {
                $query->where(function($q) use ($searchValue) {
                    $q->where('title', 'like', "%{$searchValue}%")
                      ->orWhere('description', 'like', "%{$searchValue}%");
                });
            }
            
            // Get total count before pagination
            $totalRecords = Task::count();
            $filteredRecords = $query->count();
            
            // Apply ordering and pagination
            $tasks = $query->orderBy($orderColumnName, $orderDir)
                          ->offset($start)
                          ->limit($length)
                          ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'DataTable data retrieved successfully',
                'draw' => intval($request->get('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $tasks->map(function($task) {
                    return [
                        'id' => $task->id,
                        'order' => $task->order,
                        'completed' => $task->completed,
                        'title' => $task->title,
                        'description' => $task->description,
                    ];
                }),
                'status_code' => 200,
                'timestamp' => now()->toISOString()
            ], 200);
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to process DataTable request: ' . $e->getMessage());
        }
    }

    /**
     * Handle scroll view pagination request
     */
    private function handleScrollViewRequest(Request $request, $query): JsonResponse
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            
            // Apply ordering
            $query->orderBy('order', 'asc');
            
            // Get paginated results
            $tasks = $query->paginate($perPage, ['*'], 'page', $page);
            
            return response()->json([
                'success' => true,
                'message' => 'Scroll view data retrieved successfully',
                'data' => $tasks->items(),
                'pagination' => [
                    'current_page' => $tasks->currentPage(),
                    'last_page' => $tasks->lastPage(),
                    'per_page' => $tasks->perPage(),
                    'total' => $tasks->total(),
                    'from' => $tasks->firstItem(),
                    'to' => $tasks->lastItem(),
                    'has_more_pages' => $tasks->hasMorePages()
                ],
                'status_code' => 200,
                'timestamp' => now()->toISOString()
            ], 200);
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to process scroll view request: ' . $e->getMessage());
        }
    }
}
