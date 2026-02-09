<?php

namespace App\Filament\Resources\BackupResource\Pages;

use App\Filament\Resources\BackupResource;
use App\Models\Backup;
use App\Services\ProjectBackupService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListBackups extends ListRecords
{
    protected static string $resource = BackupResource::class;

    protected static string $view = 'filament.resources.backups.list-backups';

    /**
     * Скрываем стандартный заголовок Filament,
     * чтобы не дублировалось с кастомной версткой.
     */
    public function getHeading(): string
    {
        return '';
    }

    public function getSubheading(): ?string
    {
        return null;
    }

    /**
     * Авторизация: только SUPER_ADMIN (is_admin=1) или роли admin/SUPER_ADMIN
     */
    protected function canManageBackups(): bool
    {
        $u = Auth::user();

        if (! $u) {
            return false;
        }

        if ((int) ($u->is_admin ?? 0) === 1) {
            return true;
        }

        if (method_exists($u, 'hasRole')) {
            return $u->hasRole('admin') || $u->hasRole('SUPER_ADMIN');
        }

        return false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createBackup')
                ->label(fn () => __('backup.actions.create_project'))
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(fn () => __('backup.modal.create_heading'))
                ->modalDescription(fn () => __('backup.modal.create_description'))
                ->visible(fn () => $this->canManageBackups())
                ->action(function () {
                    if (! $this->canManageBackups()) {
                        Notification::make()
                            ->title(__('backup.notifications.no_rights.title'))
                            ->danger()
                            ->send();

                        return;
                    }

                    $data = app(ProjectBackupService::class)->create('local');

                    Backup::create([
                        'name'       => $data['filename'],
                        'filename'   => $data['filename'],
                        'disk'       => $data['disk'],
                        'path'       => $data['path'],
                        'size_bytes' => $data['size_bytes'],
                        'created_by' => Auth::id(),
                    ]);

                    Notification::make()
                        ->title(__('backup.notifications.created.title'))
                        ->body($data['filename'])
                        ->success()
                        ->send();

                    $this->resetTable();
                })
                ->button(),
        ];
    }
}
