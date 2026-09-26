<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::withCount($this->progressCounts())->get();

        return response()->json([
            'message' => 'Projects retrieved successfully',
            'data' => $projects,
        ]);
    }

    public function show($id)
    {
        $project = Project::withCount($this->progressCounts())->find($id);

        if ($project) {
            return response()->json([
                'message' => 'Project retrieved successfully',
                'data' => $project,
            ]);
        }

        return response()->json(['message' => 'Project not found'], 404);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $project = Project::create([
            'user_id' => auth('sanctum')->id(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
        ]);

        $project->loadCount($this->progressCounts());

        return response()->json([
            'message' => 'Project created successfully',
            'data' => $project,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $project = Project::find($id);

        if (! $project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:pending,in_progress,completed',
        ]);

        $project->update($validated);
        $project->loadCount($this->progressCounts());

        return response()->json([
            'message' => 'Project updated successfully',
            'data' => $project,
        ]);
    }

    public function destroy($id)
    {
        $project = Project::find($id);

        if (! $project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully',
        ]);
    }

    private function progressCounts(): array
    {
        return [
            'tasks',
            'tasks as completed_tasks_count' => fn ($q) => $q->where('status', 'completed'),
        ];
    }
}
