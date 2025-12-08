<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RateLimiter
{
    public function handle(Request $request, Closure $next)
    {
        $key = 'rate_limit_' . $request->ip();
        
        if (Cache::has($key)) {
            $attempts = Cache::get($key);
            
            if ($attempts >= 5) {
                return response()->json([
                    'error' => 'Too many requests. Please try again later.'
                ], 429);
            }
        }

        Cache::increment($key);
        Cache::put($key, Cache::get($key), now()->addMinutes(1));

        return $next($request);
    }
}
