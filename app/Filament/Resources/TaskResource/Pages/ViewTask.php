<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;

class ViewTask extends ViewRecord
{
    protected static string $resource = TaskResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Task')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('title')
                            ->label('Title')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight('bold')
                            ->color('warning')
                            ->columnSpan(1),

                        TextEntry::make('assignee.name')
                            ->label('Assignee')
                            ->badge()
                            ->color('info')
                            ->columnSpan(1),

                        IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean()
                            ->columnSpan(2),

                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpan(2)
                            ->prose()
                            ->extraAttributes([
                                'class' => 'rounded-xl border border-white/10 bg-white/5 p-4 text-white/90',
                            ]),

                        // Attachments (если у тебя attachments = массив путей)
                        RepeatableEntry::make('attachments')
                            ->label('Attachments')
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
