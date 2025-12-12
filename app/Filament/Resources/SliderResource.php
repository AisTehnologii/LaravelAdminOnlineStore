<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Resources\Resource;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;

    protected static ?string $navigationIcon  = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Slider';
    protected static ?int    $navigationSort  = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Слайд (правый большой карусель)')
                    ->description('Изображения, которые крутятся справа от About-карточки на главной.')
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
                                ->label('Картинка слайда')
                                ->image()
                                ->directory('sliders')
                                ->imagePreviewHeight('150')
                                ->required(),
                        ]),

                        Forms\Components\TextInput::make('alt')
                            ->label('Alt-текст (описание картинки)')
                            ->maxLength(255)
                            ->helperText('Для SEO и доступности. Можно кратко описать, что изображено. (Поле необязательно.)'),
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

                Tables\Columns\TextColumn::make('alt')
                    ->label('Alt')
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
            'index'  => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit'   => Pages\EditSlider::route('/{record}/edit'),
        ];
    }
}
