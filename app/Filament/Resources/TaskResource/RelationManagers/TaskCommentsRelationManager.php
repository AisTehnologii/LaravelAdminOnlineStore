<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use App\Filament\Resources\TaskResource\Pages\ViewTask;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TaskCommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    /**
     * ✅ Заголовок блока "Комментарии" — переводимый
     */
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('task_comments.title');
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    /**
     * ✅ Create только на странице просмотра
     */
    public function canCreate(): bool
    {
        return $this->getPageClass() === ViewTask::class;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Textarea::make('body')
                ->label(__('task_comments.form.body'))
                ->required()
                ->rows(4),

            Forms\Components\FileUpload::make('attachments')
                ->label(__('task_comments.form.attachments'))
                ->disk('public')
                ->directory('task-comments')
                ->multiple()
                ->preserveFilenames()
                ->downloadable()
                ->openable(),

            Forms\Components\Hidden::make('user_id')
                ->default(fn () => auth()->id()),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ViewColumn::make('comment')
                    ->label('') // ✅ убираем "COMMENT" в шапке
                    ->view('filament.task-comments.comment-card'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('task_comments.actions.add'))
                    ->icon('heroicon-o-chat-bubble-left')
                    ->visible(fn () => $this->getPageClass() === ViewTask::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('task_comments.actions.edit')),

                Tables\Actions\DeleteAction::make()
                    ->label(__('task_comments.actions.delete')),
            ])
            ->paginated(false)
            ->recordUrl(null);
    }
}
