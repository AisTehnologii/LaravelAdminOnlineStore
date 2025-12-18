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
    protected static ?string $navigationGroup = 'Communication';
    protected static ?string $navigationLabel = 'Tasks';

    /**
     * Фильтруем список:
     * - "админ" (у кого есть chat.view_all) видит всё
     * - остальные видят только то, где они автор или исполнитель
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
     * - админ: все активные
     * - обычный: активные только назначенные ему
     */
    public static function getNavigationBadge(): ?string
    {
        $u = auth()->user();
        if (! $u) return null;

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
            Forms\Components\Section::make('Task')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Title')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('assigned_to')
                        ->label('Assignee')
                        ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),

                    Forms\Components\RichEditor::make('description')
                        ->label('Description')
                        ->columnSpanFull()
                        ->nullable(),

                    Forms\Components\FileUpload::make('attachments')
                        ->label('Attachments')
                        ->disk('public')
                        ->directory('tasks')
                        ->multiple()
                        ->preserveFilenames()
                        ->downloadable()
                        ->openable()
                        ->reorderable()
                        ->nullable()
                        ->columnSpanFull(),

                    // created_by прячем, заполняем автоматически
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
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Assignee')
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('comments_count')
                    ->counts('comments')
                    ->label('Comments')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'view' => Pages\ViewTask::route('/{record}'),
            'edit'   => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
