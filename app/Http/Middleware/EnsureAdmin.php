<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('filament.admin.auth.login'); // или /admin/login
        }

        // ✅ Только админы
        if ((int) Auth::user()->is_admin !== 1) {
            abort(403);
        }

        return $next($request);
    }
}
