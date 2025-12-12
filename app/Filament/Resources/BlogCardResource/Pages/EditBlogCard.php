<?php

namespace App\Filament\Resources\BlogCardResource\Pages;

use App\Filament\Resources\BlogCardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlogCard extends EditRecord
{
    protected static string $resource = BlogCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
