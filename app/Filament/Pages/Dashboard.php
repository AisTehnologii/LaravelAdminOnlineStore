<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';
    protected static ?int $navigationSort = 1;

    // чтобы это была "главная" страница /admin
    public static function getSlug(): string
    {
        return '';
    }

    // Заголовок страницы (то, что крупно сверху)
    public function getHeading(): string
    {
        return __('dashboard.page_title');
    }

    // Title в <title> и хлебных крошках
    public function getTitle(): string
    {
        return __('dashboard.page_title');
    }

    // Название в левом меню
    public static function getNavigationLabel(): string
    {
        return __('dashboard.page_title');
    }
}
