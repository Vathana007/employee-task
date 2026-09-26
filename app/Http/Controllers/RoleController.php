<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
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
            'description' => 'nullable|string'
        ]);

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
            'description' => 'nullable|string'
        ]);

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

        if (in_array(strtolower($role->name), ['admin', 'user', 'editor'])) {
            return response()->json(['message' => 'Cannot delete system roles'], 403);
        }
        $role->delete();
        return response()->json(['message' => 'Role deleted successfully']);
    }
}
