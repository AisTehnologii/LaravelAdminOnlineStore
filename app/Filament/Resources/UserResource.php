<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    /** Только главный админ (is_admin=1) видит этот ресурс */
    public static function canAccess(): bool
    {
        return (bool) (auth()->user()?->is_admin);
    }

    /** Навигация (левое меню) */
    public static function getNavigationGroup(): ?string
    {
        // было: 'Admin'
        return __('nav.groups.user');
    }

    public static function getNavigationLabel(): string
    {
        return __('user.navigation_label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('user.plural_label');
    }

    public static function getLabel(): ?string
    {
        return __('user.model_label');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('user.sections.user'))
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('user.fields.name'))
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label(__('user.fields.email'))
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    Forms\Components\Toggle::make('is_admin')
                        ->label(__('user.fields.is_admin'))
                        ->helperText(__('user.helpers.is_admin')),
                ])
                ->columns(2),

            Forms\Components\Section::make(__('user.sections.security'))
                ->schema([
                    Forms\Components\TextInput::make('password')
                        ->label(__('user.fields.password'))
                        ->password()
                        ->revealable()
                        ->helperText(__('user.helpers.password'))
                        ->dehydrated(fn ($state) => filled($state))
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? $state : null),

                    Forms\Components\TextInput::make('password_confirmation')
                        ->label(__('user.fields.password_confirmation'))
                        ->password()
                        ->revealable()
                        ->dehydrated(false)
                        ->same('password'),
                ])
                ->columns(2),

            Forms\Components\Section::make(__('user.sections.role'))
                ->schema([
                    Forms\Components\Select::make('role')
                        ->label(__('user.fields.role'))
                        ->options(fn () => Role::query()->pluck('name', 'name')->toArray())
                        ->searchable()
                        ->required()
                        ->default('content-maker')
                        ->helperText(__('user.helpers.role'))
                        ->dehydrated(false)
                        ->afterStateHydrated(function ($component, $record) {
                            if (! $record) return;
                            $component->state($record->roles()->pluck('name')->first());
                        })
                        ->saveRelationshipsUsing(function (User $record, $state) {
                            $record->syncRoles([$state]);
                        }),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('user.table.id'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('user.table.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('user.table.email'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_admin')
                    ->label(__('user.table.super'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('user.table.role'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? Str::upper($state) : __('user.common.dash')),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('user.actions.edit')),

                Tables\Actions\DeleteAction::make()
                    ->label(__('user.actions.delete'))
                    ->visible(fn (User $record) => auth()->id() !== $record->id),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label(__('user.actions.delete_selected'))
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
