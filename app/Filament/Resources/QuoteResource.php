<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Models\Quote;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Resources\Resource;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon  = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Quotes';
    protected static ?int    $navigationSort  = 45;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Отзыв / цитата')
                    ->description('Тексты, которые крутятся в блоке с кавычками (Testimonials).')
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
                                ->label('Порядок показа')
                                ->numeric()
                                ->minValue(1)
                                ->required(),

                            Forms\Components\TextInput::make('author')
                                ->label('Автор')
                                ->maxLength(255)
                                ->required(),
                        ]),

                        Forms\Components\TextInput::make('role')
                            ->label('Роль / должность')
                            ->maxLength(255)
                            ->helperText('Например: Founder, CEO / Клиент компании / Бухгалтер.'),

                        Forms\Components\Textarea::make('text')
                            ->label('Текст отзыва / цитаты')
                            ->rows(4)
                            ->required(),
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

                Tables\Columns\TextColumn::make('author')
                    ->label('Author')
                    ->limit(30)
                    ->searchable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('text')
                    ->label('Quote')
                    ->limit(80),

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
            'index'  => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'edit'   => Pages\EditQuote::route('/{record}/edit'),
        ];
    }
}
