<?php

namespace App\Filament\Widgets;

use App\Models\Revision;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

// ✅ ресурсы, на которые ты ссылался (оставляем — может пригодиться)
use App\Filament\Resources\TaskResource;
use App\Filament\Resources\ContentBlockResource;
use App\Filament\Resources\ContentSectionResource;
use App\Filament\Resources\ConversationResource;

use App\Filament\Resources\BannerResource;
use App\Filament\Resources\SliderResource;
use App\Filament\Resources\CardResource;
use App\Filament\Resources\ProjectResource;
use App\Filament\Resources\QuoteResource;
use App\Filament\Resources\BlogCardResource;

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
            ->get(['id', 'event', 'revisionable_type', 'revisionable_id', 'user_id', 'created_at']);

        return compact('items');
    }

    public function humanModel(?string $type): string
    {
        return match ($type) {
            'App\\Models\\Task' => 'Task',
            'App\\Models\\ContentBlock' => 'ContentBlock',
            'App\\Models\\ContentSection' => 'ContentSection',
            'App\\Models\\Conversation' => 'Conversation',

            'App\\Models\\Banner' => 'Banner',
            'App\\Models\\Slider' => 'Slider',
            'App\\Models\\Card' => 'Card',
            'App\\Models\\Project' => 'Project',
            'App\\Models\\Quote' => 'Quote',
            'App\\Models\\BlogCard' => 'BlogCard',

            default => class_basename($type ?? ''),
        };
    }

    /**
     * ✅ Ссылка на страницу просмотра РЕВИЗИИ (то, что тебе нужно)
     * пример: /admin/revisions/7
     */
    public function revisionUrl(int $revisionId): string
    {
        // строим через Filament panel (на случай если path панели изменится)
        $panelPath = Filament::getCurrentPanel()?->getPath() ?? 'admin';

        return url("/{$panelPath}/revisions/{$revisionId}");
    }

    /**
     * ✅ Старый метод — оставлен, но сейчас НЕ используем.
     * (если потом захочешь вести на запись, а не на ревизию)
     */
    public function recordUrl(?string $revisionableType, $revisionableId): ?string
    {
        $type = ltrim((string) $revisionableType, '\\');
        $id   = $revisionableId;

        if (! $type || ! $id) {
            return null;
        }

        $map = [
            \App\Models\Task::class           => TaskResource::class,
            \App\Models\ContentBlock::class   => ContentBlockResource::class,
            \App\Models\ContentSection::class => ContentSectionResource::class,
            \App\Models\Conversation::class   => ConversationResource::class,

            \App\Models\Banner::class         => BannerResource::class,
            \App\Models\Slider::class         => SliderResource::class,
            \App\Models\Card::class           => CardResource::class,
            \App\Models\Project::class        => ProjectResource::class,
            \App\Models\Quote::class          => QuoteResource::class,
            \App\Models\BlogCard::class       => BlogCardResource::class,
        ];

        $resource = $map[$type] ?? null;
        if (! $resource) {
            return null;
        }

        try {
            $panelId = Filament::getCurrentPanel()?->getId();

            return $resource::getUrl('edit', ['record' => $id], panel: $panelId);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
