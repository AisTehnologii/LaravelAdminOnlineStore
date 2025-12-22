<?php

namespace App\Filament\Resources\PromoBlockResource\Pages;

use App\Filament\Resources\PromoBlockResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPromoBlock extends EditRecord
{
    protected static string $resource = PromoBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
