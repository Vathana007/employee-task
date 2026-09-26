<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
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

        if (! $task) {
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
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
            'deadline' => 'nullable|date',
        ]);

        $task = Task::create([
            'project_id' => $request->project_id,
            'user_id' => $request->user_id,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => $request->status,
            'deadline' => $request->deadline,
        ]);

        Project::find($task->project_id)?->syncStatus();

        return response()->json([
            'message' => 'Task created successfully',
            'data' => $task,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $task = Task::find($id);

        if (! $task) {
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
            'deadline' => 'nullable|date',
        ]);

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

        if (! $task) {
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
}
