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
            'all' => Tab::make(__('content_section.tabs.all')),

            'banner' => Tab::make(__('content_section.tabs.banner'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'banner')),

            'slider' => Tab::make(__('content_section.tabs.slider'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'slider')),

            'card' => Tab::make(__('content_section.tabs.card'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'card')),

            'project' => Tab::make(__('content_section.tabs.project'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'project')),

            'quote' => Tab::make(__('content_section.tabs.quote'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'quote')),

            'blog_card' => Tab::make(__('content_section.tabs.blog_card'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'blog_card')),

            'promo' => Tab::make(__('content_section.tabs.promo'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'promo')),

            'catalog' => Tab::make(__('content_section.tabs.catalog'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'catalog')),
        ];
    }
}
