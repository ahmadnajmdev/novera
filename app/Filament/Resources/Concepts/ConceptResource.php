<?php

namespace App\Filament\Resources\Concepts;

use App\Filament\Resources\Concepts\Pages\CreateConcept;
use App\Filament\Resources\Concepts\Pages\EditConcept;
use App\Filament\Resources\Concepts\Pages\ListConcepts;
use App\Filament\Resources\Concepts\RelationManagers;
use App\Filament\Resources\Concepts\Schemas\ConceptForm;
use App\Filament\Resources\Concepts\Tables\ConceptsTable;
use App\Models\Concept;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ConceptResource extends Resource
{
    protected static ?string $model = Concept::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'key';

    protected static ?string $navigationLabel = 'Room types';

    protected static ?string $modelLabel = 'room type';

    protected static ?string $pluralModelLabel = 'room types';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;


    /** Search from anywhere in the panel, so nobody has to guess the screen. */
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'blurb', 'key'];
    }

    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    {
        return nv_tr($record, 'name', \App\Filament\Support\Translatable::defaultCode());
    }

    /**
     * Reference names are for machines. Confirmation dialogs and search
     * results should read back the name the editor typed.
     */
    public static function getRecordTitle(?\Illuminate\Database\Eloquent\Model $record): ?string
    {
        return $record
            ? nv_tr($record, 'name', \App\Filament\Support\Translatable::defaultCode())
            : null;
    }

    public static function form(Schema $schema): Schema
    {
        return ConceptForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConceptsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConcepts::route('/'),
            'create' => CreateConcept::route('/create'),
            'edit' => EditConcept::route('/{record}/edit'),
        ];
    }
}
