<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UpdateLastSeen
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $key = "last_seen_ping:{$userId}";

            // обновляем в БД максимум раз в 30 секунд
            if (!Cache::has($key)) {
                Cache::put($key, true, now()->addSeconds(30));

                Auth::user()->forceFill([
                    'last_seen_at' => now(),
                ])->saveQuietly();
            }
        }

        return $next($request);
    }
}
