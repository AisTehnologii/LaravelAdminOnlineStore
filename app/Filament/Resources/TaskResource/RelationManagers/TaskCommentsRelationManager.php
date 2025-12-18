<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\TaskResource\Pages\ViewTask;


class TaskCommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';
    protected static ?string $title = 'Comments';

  public function isReadOnly(): bool
{
    return false; // чтобы на View можно было создавать
}

public function canCreate(): bool
{
    // ✅ Create только на странице просмотра
    return $this->getPageClass() === ViewTask::class;
}


    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Textarea::make('body')
                ->label('Комментарий')
                ->required()
                ->rows(4),

            Forms\Components\FileUpload::make('attachments')
                ->label('Вложения')
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

   public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
{
    return $table
        ->columns([
            \Filament\Tables\Columns\ViewColumn::make('comment')
                ->view('filament.task-comments.comment-card'),
        ])
        ->headerActions([
            \Filament\Tables\Actions\CreateAction::make()
                ->label('Добавить комментарий')
                ->icon('heroicon-o-chat-bubble-left')
                ->visible(fn () => $this->getPageClass() === \App\Filament\Resources\TaskResource\Pages\ViewTask::class),
        ])
        ->actions([
            \Filament\Tables\Actions\EditAction::make(),
            \Filament\Tables\Actions\DeleteAction::make(),
        ])
        ->paginated(false) // 🔥 чтобы выглядело как список комментариев
        ->recordUrl(null); // 🔥 отключаем клики по строке
}

}
