<?php

namespace App\Http\Controllers;

use App\Models\ErrorLog;
use Illuminate\Http\Request;

class ErrorLogController extends Controller
{
    public function index()
    {
        return response()->json([
            'errors' => ErrorLog::getActiveErrors(),
            'summary' => ErrorLog::getSummary(),
        ]);
    }

    public function getByType($type)
    {
        return response()->json([
            'errors' => ErrorLog::getByType($type),
        ]);
    }

    public function getByUser($userId)
    {
        return response()->json([
            'errors' => ErrorLog::getByUser($userId),
        ]);
    }

    public function getByIp($ip)
    {
        return response()->json([
            'errors' => ErrorLog::getByIp($ip),
        ]);
    }

    public function resolve($id)
    {
        $error = ErrorLog::findOrFail($id);
        $error->resolve();

        return response()->json(['message' => 'Error marked as resolved']);
    }

    public function getSummary()
    {
        return response()->json(ErrorLog::getSummary());
    }
}
