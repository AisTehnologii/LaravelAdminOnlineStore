<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewTask extends ViewRecord
{
    protected static string $resource = TaskResource::class;

    /**
     * ✅ Убираем "View Task" / "Просмотр Task" и делаем полный перевод
     */
    public function getTitle(): string
    {
        return __('task.pages.view.title');
    }

    public function getHeading(): string
    {
        return __('task.pages.view.heading');
    }

    public function getBreadcrumb(): string
    {
        return __('task.pages.view.breadcrumb');
    }

    public function getSubheading(): ?string
    {
        return null;
    }

    public function getBreadcrumbs(): array
    {
        return [
            static::getResource()::getUrl() => static::getResource()::getPluralModelLabel(),
            '' => $this->getBreadcrumb(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make(__('task.view.section'))
                ->columns(2)
                ->schema([
                    TextEntry::make('title')
                        ->label(__('task.view.title'))
                        ->size(TextEntry\TextEntrySize::Large)
                        ->weight('bold')
                        ->color('warning')
                        ->columnSpan(1),

                    TextEntry::make('assignee.name')
                        ->label(__('task.view.assignee'))
                        ->badge()
                        ->color('info')
                        ->columnSpan(1),

                    IconEntry::make('is_active')
                        ->label(__('task.view.active'))
                        ->boolean()
                        ->columnSpan(2),

                    TextEntry::make('description')
                        ->label(__('task.view.description'))
                        ->columnSpan(2)
                        ->prose()
                        ->extraAttributes([
                            'class' => 'rounded-xl border border-white/10 bg-white/5 p-4 text-white/90',
                        ]),

                    RepeatableEntry::make('attachments')
                        ->label(__('task.view.attachments'))
                        ->schema([
                            ImageEntry::make('')
                                ->hiddenLabel()
                                ->height(220)
                                ->extraAttributes([
                                    'class' => 'rounded-xl border border-white/10 bg-black/20 p-2',
                                ])
                                ->url(fn ($state) => $state ? asset('storage/' . ltrim($state, '/')) : null)
                                ->openUrlInNewTab(),

                            TextEntry::make('')
                                ->hiddenLabel()
                                ->formatStateUsing(fn ($state) => $state ? basename($state) : null)
                                ->extraAttributes(['class' => 'text-white/70 text-xs']),
                        ])
                        ->columns(2)
                        ->columnSpan(2)
                        ->extraAttributes([
                            'class' => 'rounded-xl border border-white/10 bg-white/5 p-4',
                        ]),
                ]),
        ]);
    }
}
