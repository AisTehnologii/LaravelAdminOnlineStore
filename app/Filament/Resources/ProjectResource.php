<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
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

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?int $navigationSort = 40;

    // ✅ меню
    public static function getNavigationGroup(): ?string
    {
        return __('nav.groups.content');
    }

    public static function getNavigationLabel(): string
    {
        return __('project.page.nav_label');
    }

    public static function getLabel(): ?string
    {
        return __('project.page.nav_label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('project.page.nav_label');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        $type = 'project';

        return $form->schema([
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\Select::make('section_id')
                    ->label(__('project.form.section'))
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
                    ->label(__('project.form.locale'))
                    ->options(['en' => 'English', 'ru' => 'Русский', 'ro' => 'Română'])
                    ->default('en')
                    ->required(),

                Forms\Components\TextInput::make('position')
                    ->label(__('project.form.position'))
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]),

            Forms\Components\TextInput::make('title')
                ->label(__('project.form.title'))
                ->required()
                ->maxLength(255),

            Forms\Components\Textarea::make('description')
                ->label(__('project.form.description'))
                ->rows(4)
                ->nullable(),

            Forms\Components\FileUpload::make('image_path')
                ->label(__('project.form.image'))
                ->image()
                ->directory('projects')
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        $type = 'project';

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with('section')
                ->orderBy('section_id')
                ->orderBy('position')
            )
            ->groups([
                Group::make('section.title')
                    ->label(__('project.form.section'))
                    ->collapsible()
                    ->titlePrefixedWithLabel(false),
            ])
            ->defaultGroup('section.title')
            ->defaultSort('position')
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label(__('project.export.action'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->modalHeading(__('project.export.heading'))
                    ->form([
                        Forms\Components\Select::make('scope')
                            ->label(__('project.export.scope'))
                            ->options([
                                'all'     => __('project.export.scope_all'),
                                'section' => __('project.export.scope_section'),
                            ])
                            ->default('all')
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('section_id')
                            ->label(__('project.export.section'))
                            ->options(fn () => ContentSection::query()
                                ->where('type', $type)
                                ->orderBy('position')
                                ->pluck('title', 'id')
                                ->toArray()
                            )
                            ->visible(fn (callable $get) => $get('scope') === 'section')
                            ->searchable(),

                        Forms\Components\Select::make('format')
                            ->label(__('project.export.format'))
                            ->options([
                                'pdf' => __('project.export.pdf'),
                                'xml' => __('project.export.xml'),
                            ])
                            ->default('pdf')
                            ->required(),
                    ])
                    ->action(function (array $data, $livewire) use ($type) {
                        $columns = [
                            'section.title' => __('project.export.columns.section'),
                            'locale'        => __('project.export.columns.locale'),
                            'position'      => __('project.export.columns.position'),
                            'title'         => __('project.export.columns.title'),
                            'description'   => __('project.export.columns.description'),
                            'updated_at'    => __('project.export.columns.updated'),
                        ];

                        $query = method_exists($livewire, 'getFilteredTableQuery')
                            ? $livewire->getFilteredTableQuery()
                            : Project::query();

                        $query->with('section')->orderBy('section_id')->orderBy('position');

                        if (($data['scope'] ?? 'all') === 'section' && ! empty($data['section_id'])) {
                            $query->where('section_id', $data['section_id']);
                        }

                        $rows = $query->get();

                        $title = __('project.export.title');
                        $subtitle = now()->format(__('project.export.subtitle_fmt'));

                        if (($data['format'] ?? 'pdf') === 'xml') {
                            $xml = TableExport::toXml('export', 'row', $columns, $rows);

                            return response($xml, 200, [
                                'Content-Type'        => 'application/xml; charset=UTF-8',
                                'Content-Disposition' => 'attachment; filename="projects_' . now()->format('Ymd_His') . '.xml"',
                            ]);
                        }

                        $pdf = Pdf::loadView('exports.table-pdf', compact('title', 'subtitle', 'columns', 'rows'));

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'projects_' . now()->format('Ymd_His') . '.pdf'
                        );
                    }),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('locale')
                    ->label(__('project.table.locale'))
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('position')
                    ->label(__('project.table.position'))
                    ->sortable(),

                Tables\Columns\ImageColumn::make('image_path')
                    ->label(__('project.table.image'))
                    ->square(),

                Tables\Columns\TextColumn::make('title')
                    ->label(__('project.table.title'))
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('project.table.updated_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('section_id')
                    ->label(__('project.filters.section'))
                    ->relationship(
                        name: 'section',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn (Builder $query) => $query
                            ->where('type', $type)
                            ->orderBy('position')
                    ),

                SelectFilter::make('locale')
                    ->label(__('project.filters.locale'))
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
            'index'  => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit'   => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
