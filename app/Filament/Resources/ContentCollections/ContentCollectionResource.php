<?php

namespace App\Filament\Resources\ContentCollections;

use App\Filament\Resources\ContentCollections\Pages\CreateContentCollection;
use App\Filament\Resources\ContentCollections\Pages\EditContentCollection;
use App\Filament\Resources\ContentCollections\Pages\ListContentCollections;
use App\Filament\Resources\ContentCollections\RelationManagers;
use App\Filament\Resources\ContentCollections\Schemas\ContentCollectionForm;
use App\Filament\Resources\ContentCollections\Tables\ContentCollectionsTable;
use App\Models\ContentCollection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContentCollectionResource extends Resource
{
    protected static ?string $model = ContentCollection::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Media & lists';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Lists';

    protected static ?string $modelLabel = 'list';

    protected static ?string $pluralModelLabel = 'lists';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    public static function form(Schema $schema): Schema
    {
        return ContentCollectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentCollectionsTable::configure($table);
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
            'index' => ListContentCollections::route('/'),
            'create' => CreateContentCollection::route('/create'),
            'edit' => EditContentCollection::route('/{record}/edit'),
        ];
    }
}
