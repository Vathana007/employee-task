<?php 

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
     * Store a newly created resource in storage.
     */
    public function show($id) 
    {
        $user = User::find($id);

        if(!$user) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        } 

        return response()-> json([
            " message" => "User retrieved successfully",
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
            "password" => "required|min:8"
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password)
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

        $user->update([
            "name" => $request->name,
            "email" => $request->email
        ]);

        return response()->json([
            "message" => "User updated successfully",
            "data" => $user
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


        $user->delete();


        return response()->json([
            "message" => "User deleted successfully"
        ]);
    }
}
