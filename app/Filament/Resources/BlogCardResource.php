<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogCardResource\Pages;
use App\Models\BlogCard;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Resources\Resource;

class BlogCardResource extends Resource
{
    protected static ?string $model = BlogCard::class;

    protected static ?string $navigationIcon  = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Blog & Updates Cards';
    protected static ?int    $navigationSort  = 60;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Карточка блока «Blog & Updates»')
                    ->description('Небольшие карточки с датой и заголовком под блоком Blog & Updates.')
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

                            Forms\Components\DatePicker::make('date')
                                ->label('Дата')
                                ->required()
                                ->helperText('Дата, которая будет выводиться на карточке.'),
                        ]),

                        Forms\Components\TextInput::make('title')
                            ->label('Заголовок')
                            ->maxLength(255)
                            ->required(),

                        Forms\Components\TextInput::make('url')
                            ->label('Ссылка на статью')
                            ->maxLength(255)
                            ->helperText('Если оставить пустым, кнопка Read more не будет ссылкой.'),
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

                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->limit(60)
                    ->searchable(),

                Tables\Columns\TextColumn::make('url')
                    ->label('URL')
                    ->limit(40)
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
            'index'  => Pages\ListBlogCards::route('/'),
            'create' => Pages\CreateBlogCard::route('/create'),
            'edit'   => Pages\EditBlogCard::route('/{record}/edit'),
        ];
    }
}
