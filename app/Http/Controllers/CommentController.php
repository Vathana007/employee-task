<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::all();

        return response()->json([
            'message' => 'Comments retrieved successfully',
            'data' => $comments,
        ]);
    }

    public function show($id)
    {
        $comment = Comment::find($id);

        if (! $comment) {
            return response()->json([
                'message' => 'Comment not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Comment retrieved successfully',
            'data' => $comment,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'user_id' => 'required|exists:users,id',
            'comment' => 'required|string',
        ]);

        $comment = Comment::create([
            'task_id' => $request->task_id,
            'user_id' => $request->user_id,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Comment created successfully',
            'data' => $comment,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);

        if (! $comment) {
            return response()->json([
                'message' => 'Comment not found',
            ], 404);
        }

        $request->validate([
            'task_id' => 'sometimes|required|exists:tasks,id',
            'user_id' => 'sometimes|required|exists:users,id',
            'comment' => 'sometimes|required|string',
        ]);

        $comment->update($request->all());

        return response()->json([
            'message' => 'Comment updated successfully',
            'data' => $comment,
        ]);
    }

    public function destroy($id)
    {
        $comment = Comment::find($id);

        if (! $comment) {
            return response()->json([
                'message' => 'Comment not found',
            ], 404);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully',
        ]);
    }
}
