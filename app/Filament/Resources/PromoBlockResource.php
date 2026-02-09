<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoBlockResource\Pages;
use App\Models\PromoBlock;
use App\Models\ContentSection;
use App\Support\TableExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;

class PromoBlockResource extends Resource
{
    protected static ?string $model = PromoBlock::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 20;

    // ✅ меню (единый Контент)
    public static function getNavigationGroup(): ?string
    {
        return __('nav.groups.content');
    }

    public static function getNavigationLabel(): string
    {
        return __('promo.page.nav_label');
    }

    public static function getLabel(): ?string
    {
        return __('promo.page.nav_label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('promo.page.nav_label');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\Select::make('section_id')
                    ->label(__('promo.form.section'))
                    ->relationship(
                        name: 'section',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn (Builder $query) =>
                            $query->where('type', 'promo')->orderBy('position')
                    )
                    ->preload()
                    ->searchable()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\Hidden::make('type')->default('promo'),
                        Forms\Components\TextInput::make('title')->required(),
                        Forms\Components\TextInput::make('slug')->required(),
                        Forms\Components\TextInput::make('position')->numeric()->default(1),
                        Forms\Components\Toggle::make('is_active')->default(true),
                    ]),

                Forms\Components\Select::make('locale')
                    ->label(__('promo.form.locale'))
                    ->options([
                        'en' => 'English',
                        'ru' => 'Русский',
                        'ro' => 'Română',
                    ])
                    ->default('en')
                    ->required(),

                Forms\Components\TextInput::make('position')
                    ->label(__('promo.form.position'))
                    ->numeric()
                    ->default(1)
                    ->required(),
            ]),

            Forms\Components\TextInput::make('title')
                ->label(__('promo.form.main_title'))
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('subtitle')
                ->label(__('promo.form.subtitle'))
                ->maxLength(255),

            Forms\Components\Textarea::make('description')
                ->label(__('promo.form.description'))
                ->rows(3),

            Forms\Components\Textarea::make('description_2')
                ->label(__('promo.form.description_2'))
                ->rows(2),

            Forms\Components\Textarea::make('description_3')
                ->label(__('promo.form.description_3'))
                ->rows(2),

            Forms\Components\TextInput::make('link')
                ->label(__('promo.form.link'))
                ->url()
                ->nullable(),

            Forms\Components\FileUpload::make('image_path')
                ->label(__('promo.form.image'))
                ->image()
                ->directory('promo-blocks')
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        $type = 'promo';

        return $table
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->with('section')
                      ->orderBy('section_id')
                      ->orderBy('position')
            )
            ->groups([
                Group::make('section.title')
                    ->label(__('promo.form.section'))
                    ->collapsible(),
            ])
            ->defaultGroup('section.title')
            ->defaultSort('position')
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label(__('promo.export.action'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->modalHeading(__('promo.export.heading'))
                    ->form([
                        Forms\Components\Select::make('scope')
                            ->label(__('promo.export.scope'))
                            ->options([
                                'all'     => __('promo.export.scope_all'),
                                'section' => __('promo.export.scope_section'),
                            ])
                            ->default('all')
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('section_id')
                            ->label(__('promo.export.section'))
                            ->options(fn () =>
                                ContentSection::query()
                                    ->where('type', $type)
                                    ->orderBy('position')
                                    ->pluck('title', 'id')
                                    ->toArray()
                            )
                            ->visible(fn (callable $get) => $get('scope') === 'section')
                            ->searchable(),

                        Forms\Components\Select::make('format')
                            ->label(__('promo.export.format'))
                            ->options([
                                'pdf' => __('promo.export.pdf'),
                                'xml' => __('promo.export.xml'),
                            ])
                            ->default('pdf')
                            ->required(),
                    ])
                    ->action(function (array $data, $livewire) {
                        $columns = [
                            'section.title' => __('promo.export.columns.section'),
                            'locale'        => __('promo.export.columns.locale'),
                            'position'      => __('promo.export.columns.position'),
                            'title'         => __('promo.export.columns.title'),
                            'subtitle'      => __('promo.export.columns.subtitle'),
                            'updated_at'    => __('promo.export.columns.updated'),
                        ];

                        $query = method_exists($livewire, 'getFilteredTableQuery')
                            ? $livewire->getFilteredTableQuery()
                            : PromoBlock::query();

                        $query->with('section')
                              ->orderBy('section_id')
                              ->orderBy('position');

                        if (($data['scope'] ?? 'all') === 'section' && ! empty($data['section_id'])) {
                            $query->where('section_id', $data['section_id']);
                        }

                        $rows = $query->get();

                        $title = __('promo.export.title');
                        $subtitle = now()->format(__('promo.export.subtitle_fmt'));

                        if (($data['format'] ?? 'pdf') === 'xml') {
                            $xml = TableExport::toXml('export', 'row', $columns, $rows);

                            return response($xml, 200, [
                                'Content-Type'        => 'application/xml; charset=UTF-8',
                                'Content-Disposition' => 'attachment; filename="promo_blocks_' . now()->format('Ymd_His') . '.xml"',
                            ]);
                        }

                        $pdf = Pdf::loadView('exports.table-pdf', compact('title', 'subtitle', 'columns', 'rows'));

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'promo_blocks_' . now()->format('Ymd_His') . '.pdf'
                        );
                    }),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('locale')
                    ->label(__('promo.table.locale'))
                    ->badge(),

                Tables\Columns\TextColumn::make('position')
                    ->label(__('promo.table.position'))
                    ->sortable(),

                Tables\Columns\ImageColumn::make('image_path')
                    ->label(__('promo.table.image'))
                    ->square(),

                Tables\Columns\TextColumn::make('title')
                    ->label(__('promo.table.title'))
                    ->limit(40)
                    ->searchable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('promo.table.updated_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('section_id')
                    ->label(__('promo.filters.section'))
                    ->relationship(
                        name: 'section',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn (Builder $query) =>
                            $query->where('type', $type)->orderBy('position')
                    ),

                SelectFilter::make('locale')
                    ->label(__('promo.filters.locale'))
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
            'index'  => Pages\ListPromoBlocks::route('/'),
            'create' => Pages\CreatePromoBlock::route('/create'),
            'edit'   => Pages\EditPromoBlock::route('/{record}/edit'),
        ];
    }
}
