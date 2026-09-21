<?php

namespace App\Filament\Resources\ConceptTabs;

use App\Filament\Resources\ConceptTabs\Pages\CreateConceptTab;
use App\Filament\Resources\ConceptTabs\Pages\EditConceptTab;
use App\Filament\Resources\ConceptTabs\Pages\ListConceptTabs;
use App\Filament\Resources\ConceptTabs\Schemas\ConceptTabForm;
use App\Filament\Resources\ConceptTabs\Tables\ConceptTabsTable;
use App\Models\ConceptTab;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ConceptTabResource extends Resource
{
    protected static ?string $model = ConceptTab::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Categories';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'label';

    protected static ?string $navigationLabel = 'Room page tabs';

    protected static ?string $modelLabel = 'room page tab';

    protected static ?string $pluralModelLabel = 'room page tabs';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;

    /** Structural settings are limited to administrators. */
    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }


    /** Headings and dialogs read back the name, never the reference name. */
    public static function getRecordTitle(?\Illuminate\Database\Eloquent\Model $record): ?string
    {
        return $record
            ? nv_tr($record, 'label', \App\Filament\Support\Translatable::defaultCode())
            : null;
    }

    public static function form(Schema $schema): Schema
    {
        return ConceptTabForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConceptTabsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConceptTabs::route('/'),
            'create' => CreateConceptTab::route('/create'),
            'edit' => EditConceptTab::route('/{record}/edit'),
        ];
    }
}
