<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Chats extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Communication';
    protected static ?string $navigationLabel = 'Chats';
    protected static string $view = 'filament.pages.chats';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check();
    }

    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public static function getNavigationBadge(): ?string
    {
        $u = auth()->user();
        if (! $u) {
            return null;
        }

        // Кол-во непрочитанных сообщений во всех диалогах пользователя
        $count = DB::table('conversation_participants as cp')
            ->join('messages as m', 'm.conversation_id', '=', 'cp.conversation_id')
            ->where('cp.user_id', $u->id)
            ->where('m.user_id', '!=', $u->id)
            ->whereColumn('m.created_at', '>', DB::raw("COALESCE(cp.last_read_at, '1970-01-01 00:00:00')"))
            ->count();

        return $count > 0 ? (string) $count : null; // null => бейдж скрыт
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning'; // жёлтый
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Непрочитанные сообщения';
    }
}
