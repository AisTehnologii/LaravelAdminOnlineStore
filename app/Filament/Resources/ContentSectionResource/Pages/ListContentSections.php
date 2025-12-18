<?php

namespace App\Filament\Resources\ContentSectionResource\Pages;

use App\Filament\Resources\ContentSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListContentSections extends ListRecords
{
    protected static string $resource = ContentSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),

            'banner' => Tab::make('Banners')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'banner')),

            'slider' => Tab::make('Sliders')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'slider')),

            'card' => Tab::make('Cards')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'card')),

            'project' => Tab::make('Projects')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'project')),

            'quote' => Tab::make('Quotes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'quote')),

            'blog_card' => Tab::make('Blog Cards')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'blog_card')),
        ];
    }
}
