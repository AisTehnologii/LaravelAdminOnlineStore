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

    protected static ?string $navigationIcon  = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Access';
    protected static ?string $navigationLabel = 'History';
    protected static ?int    $navigationSort  = 50;

    // ✅ только чтение
    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function form(Form $form): Form
    {
        // form используется на View странице
        return $form->schema([
            Forms\Components\Textarea::make('old_values_pretty')
                ->label('Old values')
                ->disabled()
                ->rows(14)
                ->dehydrated(false)
                ->formatStateUsing(function ($state, Revision $record) {
                    return json_encode($record->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                }),

            Forms\Components\Textarea::make('new_values_pretty')
                ->label('New values')
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
                ->label('Очистить историю')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Очистить историю изменений')
                ->modalDescription('Это действие удалит ВСЮ историю изменений без возможности восстановления.')
                ->modalSubmitActionLabel('Да, очистить')
                ->action(function () {
                    DB::table('revisions')->truncate();

                    Notification::make()
                        ->title('История изменений очищена')
                        ->success()
                        ->send();
                }),
        ])
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('event')
                    ->badge()
                    ->label('Event')
                    ->sortable(),

                Tables\Columns\TextColumn::make('revisionable_type')
                    ->label('Model')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->searchable(),

                Tables\Columns\TextColumn::make('revisionable_id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->placeholder('—')
                    ->searchable(),

                Tables\Columns\TextColumn::make('ip')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('user_agent')
                    ->label('User agent')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->options([
                        'created' => 'created',
                        'updated' => 'updated',
                        'deleted' => 'deleted',
                    ]),

                Tables\Filters\SelectFilter::make('revisionable_type')
                    ->label('Model')
                    ->options([
                        \App\Models\Banner::class        => 'Banner',
                        \App\Models\Slider::class        => 'Slider',
                        \App\Models\Card::class          => 'Card',
                        \App\Models\Project::class       => 'Project',
                        \App\Models\Quote::class         => 'Quote',
                        \App\Models\BlogCard::class      => 'BlogCard',
                        \App\Models\ContentBlock::class  => 'ContentBlock',
                        \App\Models\ContentSection::class=> 'ContentSection',
                    ])
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                // ✅ КНОПКА ОТКАТА
                Tables\Actions\Action::make('rollback')
                    ->label('Rollback')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Revision $record) {
                        $record->rollback(); // метод в модели Revision

                        Notification::make()
                            ->title('Rollback выполнен')
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
