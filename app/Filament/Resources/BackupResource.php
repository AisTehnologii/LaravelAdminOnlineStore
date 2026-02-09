<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BackupResource\Pages;
use App\Models\Backup;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BackupResource extends Resource
{
    protected static ?string $model = Backup::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?int $navigationSort = 30;

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->is_admin;
    }

    public static function canViewAny(): bool
    {
        return static::canAccess();
    }

    public static function canCreate(): bool
    {
        return static::canAccess();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('nav.groups.access');
    }

    public static function getNavigationLabel(): string
    {
        return __('backup.navigation_label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('backup.navigation_label');
    }

    public static function getLabel(): ?string
    {
        return __('backup.navigation_label');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(fn () => __('backup.fields.name'))
                    ->searchable()
                    ->weight('bold')
                    ->wrap(),

                Tables\Columns\TextColumn::make('size_human')
                    ->label(fn () => __('backup.fields.size'))
                    ->badge(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(fn () => __('backup.fields.by'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(fn () => __('backup.fields.created'))
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label(fn () => __('backup.actions.download'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Backup $record) => route('backups.download', $record))
                    ->visible(fn () => static::canAccess())
                    ->button(),
            ])
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBackups::route('/'),
        ];
    }
}
