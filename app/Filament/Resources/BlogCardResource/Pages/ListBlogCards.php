<?php

namespace App\Filament\Resources\BlogCardResource\Pages;

use App\Filament\Resources\BlogCardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBlogCards extends ListRecords
{
    protected static string $resource = BlogCardResource::class;

    protected function getHeaderActions(): array
    {
         return [
            Actions\CreateAction::make()
                ->label('Add card')   // 🔹 текст кнопки над таблицей
                ->icon('heroicon-o-plus'), // (необязательно) иконка
        ];
    }
}
