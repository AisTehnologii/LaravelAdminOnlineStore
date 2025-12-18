<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Widgets\Widget;

class ActiveTasksWidget extends Widget
{
    protected static string $view = 'filament.widgets.active-tasks-widget';

    protected int|string|array $columnSpan = 1;

    public function getViewData(): array
    {
        $activeTotal = Task::query()
            ->where('is_active', true)
            ->count();

        // временно: пока нет assignee_id
        $myActive = null;

        $latest = Task::query()
            ->where('is_active', true)
            ->latest('id')
            ->limit(5)
            ->get(['id', 'title', 'created_at']); // <-- убрали assignee_id

        return compact('activeTotal', 'myActive', 'latest');
    }
}
