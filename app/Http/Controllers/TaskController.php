<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();

        return response()->json([
            'message' => 'Tasks retrieved successfully',
            'data' => $tasks,
        ]);
    }

    public function show($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'message' => 'Task not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Task retrieved successfully',
            'data' => $task,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        if ($error = $this->dateRangeError($validated['start_date'] ?? null, $validated['end_date'] ?? null)) {
            return $error;
        }

        $task = Task::create($validated);

        Project::find($task->project_id)?->syncStatus();

        return response()->json([
            'message' => 'Task created successfully',
            'data' => $task,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'message' => 'Task not found',
            ], 404);
        }

        $validated = $request->validate([
            'project_id' => 'sometimes|required|exists:projects,id',
            'user_id' => 'sometimes|required|exists:users,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'sometimes|required|in:low,medium,high',
            'status' => 'sometimes|required|in:pending,in_progress,completed',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        // Check the range against the saved value when only one date is sent
        $startDate = array_key_exists('start_date', $validated) ? $validated['start_date'] : $task->start_date;
        $endDate = array_key_exists('end_date', $validated) ? $validated['end_date'] : $task->end_date;

        if ($error = $this->dateRangeError($startDate, $endDate)) {
            return $error;
        }

        $oldProjectId = $task->project_id;

        $task->update($validated);

        // Keep project status in sync with its tasks
        Project::find($task->project_id)?->syncStatus();
        if ($oldProjectId !== $task->project_id) {
            Project::find($oldProjectId)?->syncStatus();
        }

        return response()->json([
            'message' => 'Task updated successfully',
            'data' => $task,
        ]);
    }

    public function destroy($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'message' => 'Task not found',
            ], 404);
        }

        $projectId = $task->project_id;

        $task->delete();

        Project::find($projectId)?->syncStatus();

        return response()->json([
            'message' => 'Task deleted successfully',
        ]);
    }

    private function dateRangeError(?string $startDate, ?string $endDate): ?JsonResponse
    {
        if ($startDate && $endDate && Carbon::parse($endDate)->lt(Carbon::parse($startDate))) {
            return response()->json([
                'message' => 'End date cannot be before start date',
                'errors' => [
                    'end_date' => ['End date cannot be before start date'],
                ],
            ], 422);
        }

        return null;
    }
}
