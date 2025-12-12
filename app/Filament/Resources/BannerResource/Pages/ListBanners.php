<?php

namespace App\Filament\Resources\BannerResource\Pages;

use App\Filament\Resources\BannerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBanners extends ListRecords
{
    protected static string $resource = BannerResource::class;

    protected function getHeaderActions(): array
    {
         return [
            Actions\CreateAction::make()
                ->label('Add banner')   // 🔹 текст кнопки над таблицей
                ->icon('heroicon-o-plus'), // (необязательно) иконка
        ];
    }
}
