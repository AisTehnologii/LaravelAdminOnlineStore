<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Models\Slider;
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

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?int $navigationSort = 20;

    // ✅ меню (единый Контент)
    public static function getNavigationGroup(): ?string
    {
        return __('nav.groups.content');
    }

    public static function getNavigationLabel(): string
    {
        return __('slider.page.nav_label');
    }

    public static function getLabel(): ?string
    {
        return __('slider.page.nav_label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('slider.page.nav_label');
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
                    ->label(__('slider.form.section'))
                    ->relationship(
                        name: 'section',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn (Builder $query) => $query->where('type', 'slider')->orderBy('position')
                    )
                    ->preload()
                    ->searchable()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\Hidden::make('type')->default('slider'),
                        Forms\Components\TextInput::make('title')->required()->maxLength(255),
                        Forms\Components\TextInput::make('slug')->required()->maxLength(255),
                        Forms\Components\TextInput::make('position')->numeric()->default(1),
                        Forms\Components\Toggle::make('is_active')->default(true),
                    ]),

                Forms\Components\Select::make('locale')
                    ->label(__('slider.form.locale'))
                    ->options(['en' => 'English', 'ru' => 'Русский', 'ro' => 'Română'])
                    ->default('en')
                    ->required(),

                Forms\Components\TextInput::make('position')
                    ->label(__('slider.form.position'))
                    ->numeric()
                    ->default(1)
                    ->required(),
            ]),

            Forms\Components\FileUpload::make('image_path')
                ->label(__('slider.form.image'))
                ->image()
                ->directory('sliders')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        $type = 'slider';

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('section')->orderBy('section_id')->orderBy('position'))
            ->groups([
                Group::make('section.title')
                    ->label(__('slider.form.section'))
                    ->collapsible()
                    ->titlePrefixedWithLabel(false),
            ])
            ->defaultGroup('section.title')
            ->defaultSort('position')
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label(__('slider.export.action'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->modalHeading(__('slider.export.heading'))
                    ->form([
                        Forms\Components\Select::make('scope')
                            ->label(__('slider.export.scope'))
                            ->options([
                                'all'     => __('slider.export.scope_all'),
                                'section' => __('slider.export.scope_section'),
                            ])
                            ->default('all')
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('section_id')
                            ->label(__('slider.export.section'))
                            ->options(fn () => ContentSection::query()
                                ->where('type', $type)
                                ->orderBy('position')
                                ->pluck('title', 'id')
                                ->toArray()
                            )
                            ->visible(fn (callable $get) => $get('scope') === 'section')
                            ->searchable(),

                        Forms\Components\Select::make('format')
                            ->label(__('slider.export.format'))
                            ->options([
                                'pdf' => __('slider.export.pdf'),
                                'xml' => __('slider.export.xml'),
                            ])
                            ->default('pdf')
                            ->required(),
                    ])
                    ->action(function (array $data, $livewire) {
                        $columns = [
                            'section.title' => __('slider.export.columns.section'),
                            'locale'        => __('slider.export.columns.locale'),
                            'position'      => __('slider.export.columns.position'),
                            'image_path'    => __('slider.export.columns.image'),
                            'updated_at'    => __('slider.export.columns.updated'),
                        ];

                        $query = method_exists($livewire, 'getFilteredTableQuery')
                            ? $livewire->getFilteredTableQuery()
                            : Slider::query();

                        $query->with('section')->orderBy('section_id')->orderBy('position');

                        if (($data['scope'] ?? 'all') === 'section' && ! empty($data['section_id'])) {
                            $query->where('section_id', $data['section_id']);
                        }

                        $rows = $query->get();

                        $title = __('slider.export.title');
                        $subtitle = now()->format(__('slider.export.subtitle_fmt'));

                        if (($data['format'] ?? 'pdf') === 'xml') {
                            $xml = TableExport::toXml('export', 'row', $columns, $rows);

                            return response($xml, 200, [
                                'Content-Type'        => 'application/xml; charset=UTF-8',
                                'Content-Disposition' => 'attachment; filename="sliders_' . now()->format('Ymd_His') . '.xml"',
                            ]);
                        }

                        $pdf = Pdf::loadView('exports.table-pdf', compact('title', 'subtitle', 'columns', 'rows'));

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'sliders_' . now()->format('Ymd_His') . '.pdf'
                        );
                    }),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('locale')
                    ->label(__('slider.table.locale'))
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('position')
                    ->label(__('slider.table.position'))
                    ->sortable(),

                Tables\Columns\ImageColumn::make('image_path')
                    ->label(__('slider.table.image'))
                    ->square(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('slider.table.updated_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('section_id')
                    ->label(__('slider.filters.section'))
                    ->relationship(
                        name: 'section',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn (Builder $query) => $query->where('type', $type)->orderBy('position')
                    ),

                SelectFilter::make('locale')
                    ->label(__('slider.filters.locale'))
                    ->options(['en' => 'English', 'ru' => 'Русский', 'ro' => 'Română']),
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
