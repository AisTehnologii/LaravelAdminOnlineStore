<?php

namespace App\Filament\Widgets;

use App\Models\Conversation;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class UnreadChatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.unread-chats-widget';

    protected int|string|array $columnSpan = 1;

    public function getViewData(): array
    {
        $userId = Auth::id();

        $unread = Conversation::query()
            ->join('conversation_participants as cp', 'cp.conversation_id', '=', 'conversations.id')
            ->where('cp.user_id', $userId)
            ->whereExists(function ($q) {
                $q->selectRaw(1)
                    ->from('messages as m')
                    ->whereColumn('m.conversation_id', 'conversations.id')
                    ->where(function ($qq) {
                        $qq->whereNull('cp.last_read_at')
                           ->orWhereColumn('m.created_at', '>', 'cp.last_read_at');
                    });
            })
            ->select('conversations.*')
            ->distinct();

        $count = (clone $unread)->count();

        $latest = (clone $unread)
            ->with(['messages' => fn ($q) => $q->latest()->limit(1)])
            ->orderByDesc('conversations.updated_at')
            ->limit(5)
            ->get();

        return compact('count', 'latest');
    }
}
