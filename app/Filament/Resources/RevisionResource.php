<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RevisionResource\Pages;
use App\Models\Revision;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class RevisionResource extends Resource
{
    protected static ?string $model = Revision::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?int $navigationSort = 50;

    // ✅ только чтение
    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    // ✅ меню (через lang)
    public static function getNavigationGroup(): ?string
    {
        return __('nav.groups.access');
    }

    public static function getNavigationLabel(): string
    {
        return __('revision.navigation_label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('revision.navigation_label');
    }

    public static function getLabel(): ?string
    {
        return __('revision.navigation_label');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Textarea::make('old_values_pretty')
                ->label(__('revision.form.old_values'))
                ->disabled()
                ->rows(14)
                ->dehydrated(false)
                ->formatStateUsing(function ($state, Revision $record) {
                    return json_encode($record->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                }),

            Forms\Components\Textarea::make('new_values_pretty')
                ->label(__('revision.form.new_values'))
                ->disabled()
                ->rows(14)
                ->dehydrated(false)
                ->formatStateUsing(function ($state, Revision $record) {
                    return json_encode($record->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                }),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('user'))
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\Action::make('clearHistory')
                    ->label(__('revision.actions.clear_history'))
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('revision.modal.clear_heading'))
                    ->modalDescription(__('revision.modal.clear_description'))
                    ->modalSubmitActionLabel(__('revision.modal.clear_submit'))
                    ->action(function () {
                        DB::table('revisions')->truncate();

                        Notification::make()
                            ->title(__('revision.notifications.cleared'))
                            ->success()
                            ->send();
                    }),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('revision.table.date'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('event')
                    ->badge()
                    ->label(__('revision.table.event'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('revisionable_type')
                    ->label(__('revision.table.model'))
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->searchable(),

                Tables\Columns\TextColumn::make('revisionable_id')
                    ->label(__('revision.table.id'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('revision.table.user'))
                    ->placeholder(__('revision.common.dash'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('ip')
                    ->label(__('revision.table.ip'))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('user_agent')
                    ->label(__('revision.table.user_agent'))
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->label(__('revision.filters.event'))
                    ->options([
                        'created' => __('revision.events.created'),
                        'updated' => __('revision.events.updated'),
                        'deleted' => __('revision.events.deleted'),
                    ]),

                Tables\Filters\SelectFilter::make('revisionable_type')
                    ->label(__('revision.filters.model'))
                    ->options([
                        \App\Models\Banner::class         => 'Banner',
                        \App\Models\Slider::class         => 'Slider',
                        \App\Models\Card::class           => 'Card',
                        \App\Models\Project::class        => 'Project',
                        \App\Models\Quote::class          => 'Quote',
                        \App\Models\BlogCard::class       => 'BlogCard',
                        \App\Models\ContentBlock::class   => 'ContentBlock',
                        \App\Models\ContentSection::class => 'ContentSection',
                    ])
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('revision.actions.view')),

                Tables\Actions\Action::make('rollback')
                    ->label(__('revision.actions.rollback'))
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Revision $record) {
                        $record->rollback();

                        Notification::make()
                            ->title(__('revision.notifications.rollback_done'))
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRevisions::route('/'),
            'view'  => Pages\ViewRevision::route('/{record}'),
        ];
    }
}
