<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Models\Quote;
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

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?int $navigationSort = 50;

    // ✅ меню
    public static function getNavigationGroup(): ?string
    {
        return __('nav.groups.content');
    }

    public static function getNavigationLabel(): string
    {
        return __('quote.page.nav_label');
    }

    public static function getLabel(): ?string
    {
        return __('quote.page.nav_label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('quote.page.nav_label');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        $type = 'quote';

        return $form->schema([
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\Select::make('section_id')
                    ->label(__('quote.form.section'))
                    ->relationship(
                        name: 'section',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn (Builder $query) => $query
                            ->where('type', $type)
                            ->orderBy('position')
                    )
                    ->preload()
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('locale')
                    ->label(__('quote.form.locale'))
                    ->options(['en' => 'English', 'ru' => 'Русский', 'ro' => 'Română'])
                    ->default('en')
                    ->required(),

                Forms\Components\TextInput::make('position')
                    ->label(__('quote.form.position'))
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]),

            Forms\Components\Textarea::make('text')
                ->label(__('quote.form.text'))
                ->rows(4)
                ->required(),

            Forms\Components\TextInput::make('author')
                ->label(__('quote.form.author'))
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('role')
                ->label(__('quote.form.role'))
                ->maxLength(255)
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        $type = 'quote';

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with('section')
                ->orderBy('section_id')
                ->orderBy('position')
            )
            ->groups([
                Group::make('section.title')
                    ->label(__('quote.form.section'))
                    ->collapsible()
                    ->titlePrefixedWithLabel(false),
            ])
            ->defaultGroup('section.title')
            ->defaultSort('position')
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label(__('quote.export.action'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->modalHeading(__('quote.export.heading'))
                    ->form([
                        Forms\Components\Select::make('scope')
                            ->label(__('quote.export.scope'))
                            ->options([
                                'all'     => __('quote.export.scope_all'),
                                'section' => __('quote.export.scope_section'),
                            ])
                            ->default('all')
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('section_id')
                            ->label(__('quote.export.section'))
                            ->options(fn () => ContentSection::query()
                                ->where('type', $type)
                                ->orderBy('position')
                                ->pluck('title', 'id')
                                ->toArray()
                            )
                            ->visible(fn (callable $get) => $get('scope') === 'section')
                            ->searchable(),

                        Forms\Components\Select::make('format')
                            ->label(__('quote.export.format'))
                            ->options([
                                'pdf' => __('quote.export.pdf'),
                                'xml' => __('quote.export.xml'),
                            ])
                            ->default('pdf')
                            ->required(),
                    ])
                    ->action(function (array $data, $livewire) use ($type) {
                        $columns = [
                            'section.title' => __('quote.export.columns.section'),
                            'locale'        => __('quote.export.columns.locale'),
                            'position'      => __('quote.export.columns.position'),
                            'author'        => __('quote.export.columns.author'),
                            'role'          => __('quote.export.columns.role'),
                            'text'          => __('quote.export.columns.text'),
                            'updated_at'    => __('quote.export.columns.updated'),
                        ];

                        $query = method_exists($livewire, 'getFilteredTableQuery')
                            ? $livewire->getFilteredTableQuery()
                            : Quote::query();

                        $query->with('section')->orderBy('section_id')->orderBy('position');

                        if (($data['scope'] ?? 'all') === 'section' && ! empty($data['section_id'])) {
                            $query->where('section_id', $data['section_id']);
                        }

                        $rows = $query->get();

                        $title = __('quote.export.title');
                        $subtitle = now()->format(__('quote.export.subtitle_fmt'));

                        if (($data['format'] ?? 'pdf') === 'xml') {
                            $xml = TableExport::toXml('export', 'row', $columns, $rows);

                            return response($xml, 200, [
                                'Content-Type'        => 'application/xml; charset=UTF-8',
                                'Content-Disposition' => 'attachment; filename="quotes_' . now()->format('Ymd_His') . '.xml"',
                            ]);
                        }

                        $pdf = Pdf::loadView('exports.table-pdf', compact('title', 'subtitle', 'columns', 'rows'));

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'quotes_' . now()->format('Ymd_His') . '.pdf'
                        );
                    }),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('locale')
                    ->label(__('quote.table.locale'))
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('position')
                    ->label(__('quote.table.position'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('author')
                    ->label(__('quote.table.author'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('text')
                    ->label(__('quote.table.text'))
                    ->limit(60),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('quote.table.updated_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('section_id')
                    ->label(__('quote.filters.section'))
                    ->relationship(
                        name: 'section',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn (Builder $query) => $query
                            ->where('type', $type)
                            ->orderBy('position')
                    ),

                SelectFilter::make('locale')
                    ->label(__('quote.filters.locale'))
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
            'index'  => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'edit'   => Pages\EditQuote::route('/{record}/edit'),
        ];
    }
}
