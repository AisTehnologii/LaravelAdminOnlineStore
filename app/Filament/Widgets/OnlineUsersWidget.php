<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\Widget;

class OnlineUsersWidget extends Widget
{
    protected static string $view = 'filament.widgets.online-users-widget';

    protected int|string|array $columnSpan = 1;

    protected static ?string $pollingInterval = '10s'; // ✅ авто-обновление каждые 10 сек

    public function getViewData(): array
    {
        $onlineWindowMinutes = 2; // лучше 2, раз мы пингуем каждые 25 сек

        $online = User::query()
            ->whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', now()->subMinutes($onlineWindowMinutes))
            ->orderByDesc('last_seen_at')
            ->limit(10)
            ->get(['id','name','email','last_seen_at']);

        $count = User::query()
            ->whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', now()->subMinutes($onlineWindowMinutes))
            ->count();

        return compact('count', 'online', 'onlineWindowMinutes');
    }
}
