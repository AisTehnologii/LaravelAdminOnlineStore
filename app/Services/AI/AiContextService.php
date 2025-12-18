<?php

namespace App\Services\AI;

use App\Models\User;
use Illuminate\Support\Facades\Route;

class AiContextService
{
    public function build(User $user): string
    {
        $route = optional(Route::current())->getName();

        // Минимальный безопасный контекст (можно расширять задачами/ревизиями)
        $lines = [];
        $lines[] = "Project: Laravel 12 + Filament v3 admin.";
        $lines[] = "User: {$user->name} ({$user->email}).";
        $lines[] = "Role(s): " . $user->getRoleNames()->implode(', ');
        $lines[] = "Current route: " . ($route ?: 'unknown');

        // SUPER_ADMIN видит больше (если нужно — добавишь тут детали)
        if ((bool) ($user->is_admin ?? false) === true) {
            $lines[] = "Access: SUPER_ADMIN (expanded context allowed).";
        } else {
            $lines[] = "Access: limited (no sensitive data, read-only).";
        }

        return implode("\n", $lines);
    }
}
