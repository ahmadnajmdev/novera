<?php

namespace App\Filament\Resources\Pages\RelationManagers;

use App\Filament\Forms\CardPicker;
use App\Filament\Support\SectionBlocks;
use App\Models\Section;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Page composition. Each row is one section; its editable fields are derived
 * from the block registry, so the form reshapes as soon as the type changes.
 */
class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $title = 'Sections';

    protected static ?string $modelLabel = 'section';

    /** Editing an existing section: the kind is settled, so only its content. */
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Text::make(fn (?Section $record) => SectionBlocks::hint($record?->type)
                ?? 'The wording and pictures for this section.')
                ->color('gray')
                ->columnSpanFull(),

            Toggle::make('is_visible')
                ->label('Show this section on the page')
                ->helperText('Turn off to hide it without deleting anything.')
                ->default(true),

            ...static::contentFields(),
        ]);
    }

    /**
     * Adding one is two questions: which kind, then its wording. Splitting
     * them keeps seventeen cards and a dozen fields off a single screen.
     */
    protected static function createSteps(): array
    {
        return [
            Step::make('Which kind of section?')
                ->icon('heroicon-o-rectangle-group')
                ->description('Pick by what it looks like')
                ->schema([
                    CardPicker::make('type')
                        ->hiddenLabel()
                        ->required()
                        ->live()
                        ->cardColumns(2)
                        ->cards(SectionBlocks::cards()),
                ]),

            Step::make('Fill it in')
                ->icon('heroicon-o-pencil')
                ->description('Wording and pictures')
                ->schema([
                    Text::make(fn (Get $get) => SectionBlocks::hint($get('type')) ?? '')
                        ->color('gray')
                        ->columnSpanFull(),

                    Toggle::make('is_visible')
                        ->label('Show this section on the page')
                        ->default(true),

                    ...static::contentFields(),
                ]),
        ];
    }

    /**
     * One group per type, shown only for the selected one. Keyed by type so
     * Filament discards the previous type's state instead of merging the two.
     */
    protected static function contentFields(): array
    {
        return collect(SectionBlocks::definitions())->keys()->map(
            fn (string $type) => Group::make(SectionBlocks::schema($type))
                ->visible(fn (Get $get, ?Section $record) => ($get('type') ?? $record?->type) === $type)
                ->columnSpanFull(),
        )->all();
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->reorderable('sort')
            ->defaultSort('sort')
            ->description('These stack down the page in this order. Drag a row by its handle to move it, '
                .'or use “Add above” to slot a new one in between.')
            ->columns([
                TextColumn::make('type')
                    ->label('Section')
                    ->formatStateUsing(fn (?string $state) => SectionBlocks::label($state))
                    ->description(fn (Section $record) => SectionBlocks::hint($record->type))
                    ->icon(fn (Section $record) => SectionBlocks::cards()[$record->type]['icon'] ?? null)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('heading')
                    ->label('Headline')
                    ->getStateUsing(fn (Section $record) => str_replace('*', '', (string) $record->text('heading', config('app.fallback_locale'))))
                    ->limit(50)
                    ->placeholder('—')
                    ->color('gray'),
                IconColumn::make('is_visible')->label('Shown')->boolean(),
            ])
            ->headerActions([
                static::addAction('create')
                    ->label('Add a section')
                    ->modalHeading('Add a section to the end of this page'),
            ])
            ->recordActions([
                static::addAction('addAbove')
                    ->label('Add above')
                    ->icon('heroicon-m-arrow-turn-left-up')
                    ->link()
                    ->modalHeading('Add a section above this one'),
                EditAction::make()->label('Edit'),
                ReplicateAction::make()
                    ->label('Duplicate')
                    ->excludeAttributes(['sort'])
                    ->successNotificationTitle('Section duplicated'),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    /**
     * One create action used in two places. As a row action it inserts above
     * that row; from the header it appends. Both walk the same two steps.
     */
    protected static function addAction(string $name): CreateAction
    {
        return CreateAction::make($name)
            ->steps(static::createSteps())
            ->modalSubmitActionLabel('Add section')
            ->createAnother(false)
            ->using(function (array $data, RelationManager $livewire, ?Model $record) {
                $page = $livewire->getOwnerRecord();

                // A row action carries the section to sit above; the header
                // action carries none and lands at the end.
                $position = $record instanceof Section
                    ? $record->sort
                    : (int) $page->sections()->max('sort') + 1;

                $page->sections()->where('sort', '>=', $position)->increment('sort');

                return $page->sections()->create([
                    ...$data,
                    'sort' => $position,
                ]);
            })
            ->successNotificationTitle('Section added');
    }
}
