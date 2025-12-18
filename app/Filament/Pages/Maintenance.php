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
    protected static ?string $navigationGroup = 'Access';
    protected static ?string $navigationLabel = 'Maintenance';
    protected static string $view = 'filament.pages.maintenance';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return (bool) (auth()->user()?->is_admin);
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
            ->statePath('data') // ✅ КЛЮЧЕВОЕ: теперь всё пишется в $this->data
            ->schema([
                Forms\Components\Toggle::make('enabled')
                    ->label('Включить режим обслуживания')
                    ->live(),

                Forms\Components\Textarea::make('message')
                    ->label('Сообщение')
                    ->rows(3),

                Forms\Components\DateTimePicker::make('until')
                    ->label('Авто-выключение (до)')
                    ->seconds(false),

                Forms\Components\TagsInput::make('allow_ips')
                    ->label('Whitelist IP'),
            ]);
    }

    public function save(): void
    {
        // ✅ теперь тут будет актуальное состояние формы
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
            ->title('Сохранено')
            ->success()
            ->send();
    }
}
