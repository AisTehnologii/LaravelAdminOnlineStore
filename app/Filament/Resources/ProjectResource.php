<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Resources\Resource;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon  = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Projects';
    protected static ?int    $navigationSort  = 40;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Проект')
                    ->description('Карточки в блоке «Our Projects» на главной.')
                    ->schema([
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\Select::make('locale')
                                ->label('Язык')
                                ->options([
                                    'en' => 'English',
                                    'ru' => 'Русский',
                                    'ro' => 'Română',
                                ])
                                ->default('en')
                                ->required(),

                            Forms\Components\TextInput::make('position')
                                ->label('Порядковый номер')
                                ->numeric()
                                ->minValue(1)
                                ->required(),

                            Forms\Components\FileUpload::make('image_path')
                                ->label('Картинка проекта')
                                ->image()
                                ->directory('projects')
                                ->imagePreviewHeight('150'),
                        ]),

                        Forms\Components\TextInput::make('title')
                            ->label('Название проекта')
                            ->maxLength(255)
                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->label('Описание')
                            ->rows(3)
                            ->helperText('Краткое описание под названием (отображается мелким текстом).'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('position')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('locale')
                    ->label('Locale')
                    ->badge()
                    ->sortable(),

                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Image')
                    ->square(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->limit(40)
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Text')
                    ->limit(60)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated at')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('position')
            ->filters([
                SelectFilter::make('locale')
                    ->label('Locale')
                    ->options([
                        'en' => 'English',
                        'ru' => 'Русский',
                        'ro' => 'Română',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit'   => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
