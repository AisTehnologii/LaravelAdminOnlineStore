<?php

namespace App\Filament\Widgets;

use App\Models\Revision;
use Filament\Widgets\Widget;

class RecentRevisionsWidget extends Widget
{
    protected static string $view = 'filament.widgets.recent-revisions-widget';

    protected int|string|array $columnSpan = 1;

    public function getViewData(): array
    {
        $items = Revision::query()
            ->with('user')
            ->latest('id')
            ->limit(7)
            ->get(['id','event','revisionable_type','revisionable_id','user_id','created_at']);

        return compact('items');
    }

    public function humanModel(?string $type): string
    {
        return match ($type) {
            'App\\Models\\Task' => 'Task',
            'App\\Models\\ContentBlock' => 'ContentBlock',
            'App\\Models\\Conversation' => 'Conversation',
            default => class_basename($type ?? ''),
        };
    }
}
