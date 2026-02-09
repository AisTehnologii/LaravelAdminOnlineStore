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
    protected static ?int    $navigationSort  = 5;

    // ✅ меню (чтобы не было дубля CONTENT/КОНТЕНТ)
    public static function getNavigationGroup(): ?string
    {
        return __('nav.groups.content');
    }

    public static function getNavigationLabel(): string
    {
        return __('content_block.page.nav_label');
    }

    public static function getLabel(): ?string
    {
        return __('content_block.page.nav_label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('content_block.page.nav_label');
    }

    protected static function sectionOptions(): array
    {
        return [
            'layout.header'   => __('content_block.sections.layout.header'),
            'layout.footer'   => __('content_block.sections.layout.footer'),
            'home.about_card' => __('content_block.sections.home.about_card'),
            'home.blog_intro' => __('content_block.sections.home.blog_intro'),
            'home.sections'   => __('content_block.sections.home.sections'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\Select::make('section')
                    ->label(__('content_block.form.section'))
                    ->options(static::sectionOptions())
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('key')
                    ->label(__('content_block.form.key'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('locale')
                    ->label(__('content_block.form.locale'))
                    ->options(['en' => 'English', 'ru' => 'Русский', 'ro' => 'Română'])
                    ->default('en')
                    ->required(),
            ]),

            Forms\Components\Textarea::make('value')
                ->label(__('content_block.form.value'))
                ->rows(10)
                ->helperText(__('content_block.form.helper'))
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('section')
                    ->label(__('content_block.table.section'))
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => static::sectionOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('locale')
                    ->label(__('content_block.table.locale'))
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('key')
                    ->label(__('content_block.table.key'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('value')
                    ->label(__('content_block.table.value'))
                    ->limit(60)
                    ->wrap(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('content_block.table.updated_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('section')
                    ->label(__('content_block.filters.section'))
                    ->options(static::sectionOptions()),

                SelectFilter::make('locale')
                    ->label(__('content_block.filters.locale'))
                    ->options(['en' => 'English', 'ru' => 'Русский', 'ro' => 'Română']),
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
