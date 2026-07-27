<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    public function index() {
        $projects = Project::all();
        return response()->json([
            "message" => "Projects retrieved successfully",
            "data" => $projects
        ]);
    }

    public function show($id) {
        $projects = Project::find($id);
        if($projects) {
            return response()->json([
                "message" => "Project retrieved successfully",
                "data" => $projects
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
            'user_id' => 2
            , 
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
        ]);

        return response()->json([
            'message' => 'Project created successfully',
            'data' => $project,
        ], 201);
    }

    public function update(Request $request, $id) {
        $project = Project::find($id);

        if(!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        $request->validate([
            "name" => "sometimes|required|string",
            "description" => "sometimes|required|string",
            "start_date" => "sometimes|required|date",
            "end_date" => "sometimes|required|date|after_or_equal:start_date"
        ]);

        $project->update($request->all());

        return response()->json([
            "message" => "Project updated successfully",
            "data" => $project
        ]);
    }

    public function destroy($id) {
        $project = Project::find($id);

        if(!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        $project->delete();

        return response()->json([
            "message" => "Project deleted successfully"
        ]);
    }
}
