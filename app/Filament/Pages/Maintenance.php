<?php

namespace App\Filament\Pages;

use App\Models\MaintenanceSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Auth;

class Maintenance extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static string $view = 'filament.pages.maintenance';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return (bool) (auth()->user()?->is_admin);
    }

    /**
     * ✅ Левое меню — перевод
     */
    public static function getNavigationGroup(): ?string
    {
        return __('maintenance.page.nav_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('maintenance.page.nav_label');
    }

    /**
     * ✅ Заголовки страницы — перевод (убираем "Maintenance" и т.п.)
     */
    public function getTitle(): string
    {
        return __('maintenance.page.title');
    }

    public function getHeading(): string
    {
        return __('maintenance.page.heading');
    }

    public function getSubheading(): ?string
    {
        return __('maintenance.page.subheading');
    }

    public function getBreadcrumb(): string
    {
        return __('maintenance.page.breadcrumb');
    }

    public function mount(): void
    {
        $s = MaintenanceSetting::current();

        $this->form->fill([
            'enabled'   => (bool) $s->enabled,
            'message'   => (string) ($s->message ?? ''),
            'until'     => $s->until,
            'allow_ips' => is_array($s->allow_ips) ? $s->allow_ips : [],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Toggle::make('enabled')
                    ->label(__('maintenance.form.enabled'))
                    ->live(),

                Forms\Components\Textarea::make('message')
                    ->label(__('maintenance.form.message'))
                    ->rows(3)
                    ->helperText(__('maintenance.form.message_help')),

                Forms\Components\DateTimePicker::make('until')
                    ->label(__('maintenance.form.until'))
                    ->seconds(false)
                    ->helperText(__('maintenance.form.until_help')),

                Forms\Components\TagsInput::make('allow_ips')
                    ->label(__('maintenance.form.allow_ips'))
                    ->helperText(__('maintenance.form.allow_ips_help')),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $s = MaintenanceSetting::current();

        $s->fill([
            'enabled'    => (bool) ($state['enabled'] ?? false),
            'message'    => $state['message'] ?? null,
            'until'      => $state['until'] ?? null,
            'allow_ips'  => array_values(array_filter((array) ($state['allow_ips'] ?? []))),
            'updated_by' => Auth::id(),
        ])->save();

        Notification::make()
            ->title(__('maintenance.notifications.saved_title'))
            ->body(__('maintenance.notifications.saved_body'))
            ->success()
            ->send();
    }
}
