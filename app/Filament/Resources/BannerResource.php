<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Resources\Resource;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon  = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Banners';
    protected static ?int    $navigationSort  = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Слайд баннера')
                    ->description('Это крупные слайды в верхней части страницы (hero). Для каждого языка можно задать свою версию.')
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
                                ->required()
                                ->helperText('Для какого языка показывается этот баннер.'),

                            Forms\Components\TextInput::make('position')
                                ->label('Порядковый номер')
                                ->numeric()
                                ->minValue(1)
                                ->required()
                                ->helperText('Чем меньше число, тем раньше баннер появится в слайдере.'),

                            Forms\Components\FileUpload::make('image_path')
                                ->label('Фоновая картинка')
                                ->image()
                                ->directory('banners')
                                ->imagePreviewHeight('150')
                                ->required()
                                ->helperText('Картинка, которая будет справа в слайдере (фон баннера).'),
                        ]),

                        Forms\Components\TextInput::make('title')
                            ->label('Заголовок')
                            ->maxLength(255)
                            ->required()
                            ->helperText('Крупный заголовок на баннере.'),

                        Forms\Components\Textarea::make('text')
                            ->label('Описание под заголовком')
                            ->rows(3)
                            ->helperText('Краткое описание. Можно оставить пустым.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('position')
                    ->label('Slide #')
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

                Tables\Columns\TextColumn::make('text')
                    ->label('Text preview')
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
            'index'  => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit'   => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
