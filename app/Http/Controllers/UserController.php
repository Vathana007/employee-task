<?php 

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json([
            "message" => "Users retrieved successfully",
            "data" => $users
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id) 
    {
        $user = User::find($id);

        if(!$user) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        } 

        return response()->json([
            "message" => "User retrieved successfully",
            "data" => $user
        ]);
    }


    /**
     * Create user
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "email" => "required|email|unique:users",
            "password" => "required|min:8",
            "role" => "nullable|string|exists:roles,name"
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password),
            "role" => $request->role ?? 'user'
        ]);

        return response()->json([
            "message" => "User created successfully",
            "data" => $user
        ], 201);
    }


    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if(!$user) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }

        $validated = $request->validate([
            "name" => "nullable|string",
            "email" => "nullable|email|unique:users,email," . $id,
            "role" => "nullable|string|exists:roles,name",
            "password" => "nullable|string|min:8"
        ]);

        // Only update provided fields
        if ($request->filled('name')) {
            $user->name = $request->name;
        }
        if ($request->filled('email')) {
            $user->email = $request->email;
        }
        if ($request->filled('role')) {
            $user->role = $request->role;
        }
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return response()->json([
            "message" => "User updated successfully",
            "data" => $user
        ]);
    }

    /**
     * Upload user avatar
     */
    public function uploadAvatar(Request $request, $id)
    {
        $user = User::find($id);

        if(!$user) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }

        $request->validate([
            'avatar' => 'required|image|mimes:png,jpg,jpeg,webp|max:2048'
        ]);

        // Delete old avatar if exists
        if ($user->avatar_url) {
            $oldPath = str_replace('/storage/', '', $user->avatar_url);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar_url = '/storage/' . $path;
        $user->save();

        return response()->json([
            "message" => "Avatar uploaded successfully",
            "data" => [
                "avatar_url" => $user->avatar_url
            ]
        ]);
    } 

    /**
     * Change user password
     */
    public function changePassword(Request $request, $id)
    {
        $user = User::find($id);

        if(!$user) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8'
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                "message" => "Current password is incorrect"
            ], 422);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            "message" => "Password changed successfully"
        ]);
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {

        $user = User::find($id);


        if (!$user) {
            return response()->json([
                "message" => "User not found"
            ],404);
        }

        // Delete avatar if exists
        if ($user->avatar_url) {
            $path = str_replace('/storage/', '', $user->avatar_url);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $user->delete();


        return response()->json([
            "message" => "User deleted successfully"
        ]);
    }
}
