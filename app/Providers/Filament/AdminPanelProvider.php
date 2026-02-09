<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\UpdateLastSeen;
use Filament\Navigation\MenuItem;

use Filament\Support\Facades\FilamentView;
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $cur = app()->getLocale();
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
->authGuard('web')
            ->brandName('Marga Admin')

            ->brandLogo(fn () => view('filament.logo'))
->userMenuItems([
    'locale_ru' => MenuItem::make()
        ->label('Русский')
        ->icon('heroicon-o-language')
        ->url('/set-locale/ru'),

    'locale_ro' => MenuItem::make()
        ->label('Română')
        ->icon('heroicon-o-language')
        ->url('/set-locale/ro'),

    'locale_en' => MenuItem::make()
        ->label('English')
        ->icon('heroicon-o-language')
        ->url('/set-locale/en'),
])
            

            ->colors([
                'primary' => Color::Amber,
            ])

            // ✅ твои стили (как было)
            ->renderHook(
                'panels::head.end',
                fn () => view('filament.custom-styles')
            )

            // ✅ НОВОЕ: сворачивать группы по умолчанию (JS внизу)
            ->renderHook(
                'panels::scripts.after',
                fn () => view('filament.hooks.collapse-groups')
            )

            ->renderHook(
    'panels::body.end',
    fn () => view('filament.ai.drawer')
)

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
           ->middleware([
    DisableBladeIconComponents::class,
    DispatchServingFilamentEvent::class,
    UpdateLastSeen::class,
    \App\Http\Middleware\SetLocaleFromRequest::class,
])
->authMiddleware([
    Authenticate::class,
])
            ->plugins([
                FilamentShieldPlugin::make(),
            ]);
    }
}
