<?php

namespace App\Filament\Resources\BlogCardResource\Pages;

use App\Filament\Resources\BlogCardResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogCard extends CreateRecord
{
    protected static string $resource = BlogCardResource::class;
}
