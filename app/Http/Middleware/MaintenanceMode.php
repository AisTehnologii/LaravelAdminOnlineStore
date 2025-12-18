<?php

namespace App\Http\Middleware;

use App\Models\MaintenanceSetting;
use Closure;
use Illuminate\Http\Request;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        // ✅ НЕ блокируем админку Filament
        if (
            $request->is('admin') || $request->is('admin/*')
            // ✅ НЕ блокируем Livewire (иначе Filament формы не сохраняются)
            || $request->is('livewire/*')
            // ✅ НЕ блокируем ассеты Filament
            || $request->is('filament/*')
        ) {
            return $next($request);
        }

        $settings = MaintenanceSetting::current();

        // авто-выключение по времени
        // авто-выключение по времени
if ($settings->enabled && $settings->until) {
    // until уже Carbon из-за casts
    if (now()->greaterThanOrEqualTo($settings->until)) {
        $settings->forceFill([
            'enabled' => false,
            'until' => null,
        ])->saveQuietly();

        $settings->refresh();
    }
}


        if (! $settings->enabled) {
            return $next($request);
        }

        // whitelist IP
        $ip = $request->ip();
        $allow = $settings->allow_ips ?? [];

        if (in_array($ip, $allow, true)) {
            return $next($request);
        }

        return response()->view('maintenance', [
            'message' => $settings->message ?: 'Сайт временно на обслуживании. Попробуйте позже.',
        ], 503);
    }
}
