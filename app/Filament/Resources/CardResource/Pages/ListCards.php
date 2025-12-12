<?php

namespace App\Filament\Resources\CardResource\Pages;

use App\Filament\Resources\CardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCards extends ListRecords
{
    protected static string $resource = CardResource::class;

    protected function getHeaderActions(): array
    {
         return [
            Actions\CreateAction::make()
                ->label('Add card')   // 🔹 текст кнопки над таблицей
                ->icon('heroicon-o-plus'), // (необязательно) иконка
        ];
    }
}
