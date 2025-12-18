<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentBlockResource\Pages;
use App\Models\ContentBlock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class ContentBlockResource extends Resource
{
    protected static ?string $model = ContentBlock::class;

    protected static ?string $navigationIcon  = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int    $navigationSort  = 5;
    protected static ?string $navigationLabel = 'Content blocks';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\Select::make('section')
                    ->options([
                        'layout.header'    => 'Layout: Header',
                        'layout.footer'    => 'Layout: Footer',
                        'home.about_card'  => 'Home: About card',
                        'home.blog_intro'  => 'Home: Blog intro',
                        'home.sections'    => 'Home: Section titles',
                    ])
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('key')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('locale')
                    ->options(['en' => 'English', 'ru' => 'Русский', 'ro' => 'Română'])
                    ->default('en')
                    ->required(),
            ]),

            Forms\Components\Textarea::make('value')
                ->rows(10)
                ->label('Value (HTML allowed)')
                ->helperText('Можно вставлять HTML. На фронте у тебя выводится через {!! !!}.')
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('section')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('locale')->badge()->sortable(),
                Tables\Columns\TextColumn::make('key')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('value')->limit(60)->wrap(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('section')->options([
                    'layout.header'    => 'Layout: Header',
                    'layout.footer'    => 'Layout: Footer',
                    'home.about_card'  => 'Home: About card',
                    'home.blog_intro'  => 'Home: Blog intro',
                    'home.sections'    => 'Home: Section titles',
                ]),
                SelectFilter::make('locale')->options(['en' => 'English', 'ru' => 'Русский', 'ro' => 'Română']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListContentBlocks::route('/'),
            'create' => Pages\CreateContentBlock::route('/create'),
            'edit'   => Pages\EditContentBlock::route('/{record}/edit'),
        ];
    }
}
