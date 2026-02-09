<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('onec_exchange')
                ->label('Выполнить обмен с 1С')
                ->icon('heroicon-o-arrow-path')
                ->url(url('/1c/exchange?type=sale&mode=query'))
                ->openUrlInNewTab(),
        ];
    }
}
