<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 30;

    public static function getNavigationGroup(): ?string
    {
        return __('nav.groups.shop');
    }

    public static function getNavigationLabel(): string
    {
        return __('product.page.nav_label');
    }

    public static function getLabel(): ?string
    {
        return __('product.page.nav_label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('product.page.nav_label');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\Select::make('section_id')
                    ->label(__('product.form.section'))
                    ->relationship(
                        name: 'section',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn (Builder $query) =>
                            $query->where('type', 'catalog')->orderBy('position')
                    )
                    ->preload()
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('locale')
                    ->label(__('product.form.locale'))
                    ->options(['en' => 'English', 'ru' => 'Русский', 'ro' => 'Română'])
                    ->required(),

                Forms\Components\TextInput::make('position')
                    ->label(__('product.form.position'))
                    ->numeric()
                    ->required(),
            ]),

            Forms\Components\Toggle::make('is_active')
                ->label(__('product.form.active'))
                ->default(true),

            Forms\Components\Section::make(__('product.form.preview.title'))
                ->schema([
                    Forms\Components\TextInput::make('announce_title')
                        ->label(__('product.form.preview.title_field')),

                    Forms\Components\Textarea::make('announce_description')
                        ->label(__('product.form.preview.desc')),

                    Forms\Components\FileUpload::make('announce_image_path')
                        ->label(__('product.form.preview.image'))
                        ->disk('public')                 // ✅ важно
                        ->visibility('public')           // ✅ важно
                        ->image()
                        // directory можно оставить — он влияет только на новые загрузки
                        ->directory('products/announce'),
                ])
                ->columns(2),

            Forms\Components\Section::make(__('product.form.details.title'))
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label(__('product.form.details.title_field'))
                        ->required(),

                    Forms\Components\Textarea::make('description')
                        ->label(__('product.form.details.desc')),

                    Forms\Components\Textarea::make('description_extra')
                        ->label(__('product.form.details.extra_desc')),

                    Forms\Components\Repeater::make('images')
                        ->relationship()
                        ->label(__('product.form.details.images'))
                        ->schema([
                            Forms\Components\FileUpload::make('image_path')
                                ->label(__('product.form.details.image'))
                                ->disk('public')         // ✅ важно
                                ->visibility('public')   // ✅ важно
                                ->image()
                                ->directory('products/details')
                                ->required(),

                            Forms\Components\TextInput::make('position')->numeric(),
                        ])
                        ->orderColumn('position')
                        ->collapsed(),
                ])
                ->columns(2),

            Forms\Components\Section::make(__('product.form.price.title'))
                ->schema([
                    Forms\Components\TextInput::make('price')
                        ->label(__('product.form.price.price'))
                        ->numeric(),

                    Forms\Components\TextInput::make('sale_price')
                        ->label(__('product.form.price.sale_price'))
                        ->numeric(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->with('section')->orderBy('section_id')->orderBy('position')
            )
            ->groups([
                Group::make('section.title')->label(__('product.table.section'))->collapsible(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('locale')->label(__('product.table.locale'))->badge(),
                Tables\Columns\IconColumn::make('is_active')->label(__('product.table.active'))->boolean(),
                Tables\Columns\TextColumn::make('position')->label(__('product.table.position'))->sortable(),

                Tables\Columns\ImageColumn::make('announce_image_path')
                    ->label(__('product.table.image'))
                    ->disk('public')      // ✅ важно
                    ->visibility('public')
                    ->square(),

                Tables\Columns\TextColumn::make('title')->label(__('product.table.title'))->searchable(),
                Tables\Columns\TextColumn::make('sale_price')->label(__('product.table.sale'))->sortable(),
                Tables\Columns\TextColumn::make('price')->label(__('product.table.price'))->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->label(__('product.table.updated'))->dateTime(),
            ])
            ->filters([
                SelectFilter::make('section_id')
                    ->label(__('product.filters.section'))
                    ->relationship('section', 'title'),

                SelectFilter::make('locale')->label(__('product.filters.locale'))
                    ->options(['en' => 'English', 'ru' => 'Русский', 'ro' => 'Română']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
