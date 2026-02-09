<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
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

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?int $navigationSort = 10;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('nav.groups.content');
    }

    public static function getNavigationLabel(): string
    {
        return __('banner.navigation_label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('banner.navigation_label');
    }

    public static function getLabel(): ?string
    {
        return __('banner.navigation_label');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\Select::make('section_id')
                    ->label(fn () => __('banner.fields.section'))
                    ->relationship(
                        name: 'section',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn (Builder $query) => $query->where('type', 'banner')->orderBy('position')
                    )
                    ->preload()
                    ->searchable()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\Hidden::make('type')->default('banner'),

                        Forms\Components\TextInput::make('title')
                            ->label(fn () => __('banner.fields.title'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('slug')
                            ->label(fn () => __('banner.fields.slug'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('position')
                            ->label(fn () => __('banner.fields.position'))
                            ->numeric()
                            ->default(1),

                        Forms\Components\Toggle::make('is_active')
                            ->label(fn () => __('banner.fields.is_active'))
                            ->default(true),
                    ]),

                Forms\Components\Select::make('locale')
                    ->label(fn () => __('banner.fields.locale'))
                    ->options([
                        'en' => __('banner.locales.en'),
                        'ru' => __('banner.locales.ru'),
                        'ro' => __('banner.locales.ro'),
                    ])
                    ->default('en')
                    ->required(),

                Forms\Components\TextInput::make('position')
                    ->label(fn () => __('banner.fields.position'))
                    ->numeric()
                    ->default(1)
                    ->required(),
            ]),

            Forms\Components\TextInput::make('title')
                ->label(fn () => __('banner.fields.title'))
                ->required()
                ->maxLength(255),

            Forms\Components\Textarea::make('text')
                ->label(fn () => __('banner.fields.text'))
                ->rows(3)
                ->nullable(),

            Forms\Components\FileUpload::make('image_path')
                ->label(fn () => __('banner.fields.image'))
                ->image()
                ->directory('banners')
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        $type = 'banner';

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with('section')
                ->orderBy('section_id')
                ->orderBy('position')
            )
            ->groups([
                Group::make('section.title')
                    ->label(fn () => __('banner.fields.section'))
                    ->collapsible()
                    ->titlePrefixedWithLabel(false),
            ])
            ->defaultGroup('section.title')
            ->defaultSort('position')
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label(fn () => __('banner.export.action'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->modalHeading(fn () => __('banner.export.modal_heading'))
                    ->form([
                        Forms\Components\Select::make('scope')
                            ->label(fn () => __('banner.export.scope'))
                            ->options([
                                'all'     => __('banner.export.scope_all'),
                                'section' => __('banner.export.scope_section'),
                            ])
                            ->default('all')
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('section_id')
                            ->label(fn () => __('banner.export.section'))
                            ->options(fn () => ContentSection::query()
                                ->where('type', $type)
                                ->orderBy('position')
                                ->pluck('title', 'id')
                                ->toArray()
                            )
                            ->visible(fn (callable $get) => $get('scope') === 'section')
                            ->searchable(),

                        Forms\Components\Select::make('format')
                            ->label(fn () => __('banner.export.format'))
                            ->options([
                                'pdf' => __('banner.export.pdf'),
                                'xml' => __('banner.export.xml'),
                            ])
                            ->default('pdf')
                            ->required(),
                    ])
                    ->action(function (array $data, $livewire) use ($type) {
                        $columns = [
                            'section.title' => __('banner.fields.section'),
                            'locale'        => __('banner.fields.locale'),
                            'position'      => __('banner.fields.position'),
                            'title'         => __('banner.fields.title'),
                            'updated_at'    => __('banner.fields.updated_at'),
                        ];

                        $query = method_exists($livewire, 'getFilteredTableQuery')
                            ? $livewire->getFilteredTableQuery()
                            : Banner::query();

                        $query->with('section')->orderBy('section_id')->orderBy('position');

                        if (($data['scope'] ?? 'all') === 'section' && ! empty($data['section_id'])) {
                            $query->where('section_id', $data['section_id']);
                        }

                        $rows = $query->get();

                        $title = __('banner.export.pdf_title');
                        $subtitle = now()->format('Y-m-d H:i');

                        if (($data['format'] ?? 'pdf') === 'xml') {
                            $xml = TableExport::toXml('export', 'row', $columns, $rows);

                            return response($xml, 200, [
                                'Content-Type'        => 'application/xml; charset=UTF-8',
                                'Content-Disposition' => 'attachment; filename="banners_' . now()->format('Ymd_His') . '.xml"',
                            ]);
                        }

                        $pdf = Pdf::loadView('exports.table-pdf', [
                            'title'    => $title,
                            'subtitle' => $subtitle,
                            'columns'  => $columns,
                            'rows'     => $rows,
                        ]);

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'banners_' . now()->format('Ymd_His') . '.pdf'
                        );
                    }),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('locale')
    ->label(fn () => __('banner.fields.locale'))
    ->badge()
    ->sortable(),

Tables\Columns\TextColumn::make('position')
    ->label(fn () => __('banner.fields.position'))
    ->sortable(),


                Tables\Columns\ImageColumn::make('image_path')
                    ->square()
                    ->label(fn () => __('banner.fields.image')),

                Tables\Columns\TextColumn::make('title')
                    ->label(fn () => __('banner.fields.title'))
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(fn () => __('banner.fields.updated_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('section_id')
                    ->label(fn () => __('banner.fields.section'))
                    ->relationship(
                        name: 'section',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn (Builder $query) => $query->where('type', $type)->orderBy('position')
                    ),

                SelectFilter::make('locale')
                    ->label(fn () => __('banner.fields.locale'))
                    ->options([
                        'en' => __('banner.locales.en'),
                        'ru' => __('banner.locales.ru'),
                        'ro' => __('banner.locales.ro'),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
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
