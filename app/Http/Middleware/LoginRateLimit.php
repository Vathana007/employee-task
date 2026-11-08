<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\ErrorLog;

class LoginRateLimit
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();
        $cacheKey = "login_attempts_{$ip}";
        $maxAttempts = 5;
        $decayMinutes = 1;

        $attempts = Cache::get($cacheKey, 0);

        // Only log if exceeded limit
        if ($attempts >= $maxAttempts) {
            ErrorLog::create([
                'error_type' => ErrorLog::TYPE_LOGIN_FAILED,
                'message' => "Login rate limit exceeded from IP: {$ip}",
                'ip_address' => $ip,
                'user_agent' => $request->userAgent(),
                'method' => $request->method(),
                'endpoint' => $request->path(),
                'status' => 'active',
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Too many login attempts. Please try again in 1 minute.',
                'code' => 429,
            ], 429);
        }

        Cache::put($cacheKey, $attempts + 1, now()->addMinutes($decayMinutes));
        return $next($request);
    }
}
