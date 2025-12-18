<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Admin';
    protected static ?string $navigationLabel = 'Users';
    protected static ?string $modelLabel      = 'User';
    protected static ?string $pluralModelLabel = 'Users';

    /** Только главный админ (is_admin=1) видит этот ресурс */
    public static function canAccess(): bool
    {
        return (bool) (auth()->user()?->is_admin);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('User')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    Forms\Components\Toggle::make('is_admin')
                        ->label('Super admin (full access)')
                        ->helperText('Полный доступ ко всей админке, независимо от ролей.'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Security')
                ->schema([
                    Forms\Components\TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->revealable()
                        ->helperText('Оставь пустым, если не нужно менять пароль.')
                        // В БД пишем только если заполнено
                        ->dehydrated(fn ($state) => filled($state))
                        // cast hashed в модели сам захеширует
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? $state : null),

                    Forms\Components\TextInput::make('password_confirmation')
                        ->label('Confirm password')
                        ->password()
                        ->revealable()
                        ->dehydrated(false)
                        ->same('password'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Role')
                ->schema([
                    Forms\Components\Select::make('role')
                        ->label('Role')
                        ->options(fn () => Role::query()->pluck('name', 'name')->toArray())
                        ->searchable()
                        ->required()
                        ->default('content-maker')
                        ->helperText('Назначь роль пользователю. Для is_admin можно оставить admin.')
                        ->dehydrated(false)
                        ->afterStateHydrated(function ($component, $record) {
    if (! $record) return;
    $component->state($record->roles()->pluck('name')->first());
})

                        ->saveRelationshipsUsing(function (\App\Models\User $record, $state) {
    // назначаем ровно выбранную роль
    $record->syncRoles([$state]);
})

                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_admin')
                    ->boolean()
                    ->label('Super'),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? Str::upper($state) : '—'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),

                // Удалять пользователей можно (если хочешь запретить — убери DeleteAction)
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (User $record) => auth()->id() !== $record->id), // нельзя удалить себя
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn () => true),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
