<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    private const AVAILABLE_PERMISSIONS = [
        'manage_users',
        'manage_roles',
        'manage_projects',
        'manage_tasks',
        'view_all_projects',
        'delete_any_project',
        'delete_any_task',
        'view_admin_logs',
        'approve_work',
        'update_task_status',
        'create_projects',
        'edit_own_projects',
        'delete_own_projects',
        'view_assigned_projects',
        'view_assigned_tasks',
        'update_own_task_status',
    ];

    public function index()
    {
        return response()->json([
            "data" => Role::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles',
            'description' => 'nullable|string',
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:' . implode(',', self::AVAILABLE_PERMISSIONS)],
        ]);

        $validated['permissions'] = $validated['permissions'] ?? [];

        $role = Role::create($validated);
        return response()->json([
            "data" => $role
        ], 201);
    }

    public function show($id)
    {
        $role = Role::find($id);
        if(!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }
        return response()->json([
            "data" => $role
        ]);
    }

    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        if(!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:' . implode(',', self::AVAILABLE_PERMISSIONS)],
        ]);

        $validated['permissions'] = $validated['permissions'] ?? $role->permissions ?? [];

        $role->update($validated);
        return response()->json([
            "message" => "Role updated successfully",
            "data" => $role
        ]);
    }

    public function destroy($id)
    {
        $role = Role::find($id);
        if(!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        if (in_array(strtolower($role->name), ['admin', 'head', 'senior', 'user'])) {
            return response()->json(['message' => 'Cannot delete system roles'], 403);
        }
        $role->delete();
        return response()->json(['message' => 'Role deleted successfully']);
    }
}
