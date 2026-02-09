<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers\TaskCommentsRelationManager;
use App\Models\Task;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    /**
     * ✅ Переводы в левом меню
     */
    public static function getNavigationGroup(): ?string
    {
        return __('task.page.nav_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('task.page.nav_label');
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('task.page.badge_tooltip');
    }

    /**
     * ✅ Переводы для Filament (breadcumbs / titles по умолчанию)
     */
    public static function getModelLabel(): string
    {
        return __('task.resource.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('task.resource.plural');
    }

    /**
     * Фильтр списка:
     * - admin (chat.view_all) видит всё
     * - остальные только своё (создатель или исполнитель)
     */
    public static function getEloquentQuery(): Builder
    {
        $q = parent::getEloquentQuery();

        $u = auth()->user();
        if (! $u) {
            return $q->whereRaw('1=0');
        }

        if ($u->can('chat.view_all')) {
            return $q;
        }

        return $q->where(function ($qq) use ($u) {
            $qq->where('created_by', $u->id)
                ->orWhere('assigned_to', $u->id);
        });
    }

    /**
     * Бейдж в меню: активные задачи
     */
    public static function getNavigationBadge(): ?string
    {
        $u = auth()->user();
        if (! $u) {
            return null;
        }

        $q = Task::query()->where('is_active', true);

        if (! $u->can('chat.view_all')) {
            $q->where('assigned_to', $u->id);
        }

        $count = $q->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('task.form.section'))
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label(__('task.form.title'))
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('assigned_to')
                        ->label(__('task.form.assignee'))
                        ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Forms\Components\Toggle::make('is_active')
                        ->label(__('task.form.active'))
                        ->default(true),

                    Forms\Components\RichEditor::make('description')
                        ->label(__('task.form.description'))
                        ->columnSpanFull()
                        ->nullable(),

                    Forms\Components\FileUpload::make('attachments')
                        ->label(__('task.form.attachments'))
                        ->disk('public')
                        ->directory('tasks')
                        ->multiple()
                        ->preserveFilenames()
                        ->downloadable()
                        ->openable()
                        ->reorderable()
                        ->nullable()
                        ->columnSpanFull(),

                    Forms\Components\Hidden::make('created_by')
                        ->default(fn () => auth()->id()),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('task.table.active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label(__('task.table.title'))
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('assignee.name')
                    ->label(__('task.table.assignee'))
                    ->placeholder(__('task.table.dash'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('comments_count')
                    ->counts('comments')
                    ->label(__('task.table.comments'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('task.table.updated'))
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('task.filters.active')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('task.actions.view')),

                Tables\Actions\EditAction::make()
                    ->label(__('task.actions.edit')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('task.actions.delete_selected')),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TaskCommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'view'   => Pages\ViewTask::route('/{record}'),
            'edit'   => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
