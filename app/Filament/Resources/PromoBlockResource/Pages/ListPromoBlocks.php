<?php

namespace App\Filament\Resources\PromoBlockResource\Pages;

use App\Filament\Resources\PromoBlockResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPromoBlocks extends ListRecords
{
    protected static string $resource = PromoBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
