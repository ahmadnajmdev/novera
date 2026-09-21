<?php

namespace App\Filament\Resources\MaterialGroups;

use App\Filament\Resources\MaterialGroups\Pages\CreateMaterialGroup;
use App\Filament\Resources\MaterialGroups\Pages\EditMaterialGroup;
use App\Filament\Resources\MaterialGroups\Pages\ListMaterialGroups;
use App\Filament\Resources\MaterialGroups\Schemas\MaterialGroupForm;
use App\Filament\Resources\MaterialGroups\Tables\MaterialGroupsTable;
use App\Models\MaterialGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MaterialGroupResource extends Resource
{
    protected static ?string $model = MaterialGroup::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Categories';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Material groups';

    protected static ?string $modelLabel = 'material group';

    protected static ?string $pluralModelLabel = 'material groups';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    /** Structural settings are limited to administrators. */
    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }


    /** Headings and dialogs read back the name, never the reference name. */
    public static function getRecordTitle(?\Illuminate\Database\Eloquent\Model $record): ?string
    {
        return $record
            ? nv_tr($record, 'name', \App\Filament\Support\Translatable::defaultCode())
            : null;
    }

    public static function form(Schema $schema): Schema
    {
        return MaterialGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaterialGroupsTable::configure($table);
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
            'index' => ListMaterialGroups::route('/'),
            'create' => CreateMaterialGroup::route('/create'),
            'edit' => EditMaterialGroup::route('/{record}/edit'),
        ];
    }
}
