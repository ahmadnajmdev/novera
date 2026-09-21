<?php

namespace App\Filament\Resources\Styles;

use App\Filament\Resources\Styles\Pages\CreateStyle;
use App\Filament\Resources\Styles\Pages\EditStyle;
use App\Filament\Resources\Styles\Pages\ListStyles;
use App\Filament\Resources\Styles\Schemas\StyleForm;
use App\Filament\Resources\Styles\Tables\StylesTable;
use App\Models\Style;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StyleResource extends Resource
{
    protected static ?string $model = Style::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'key';

    protected static ?string $navigationLabel = 'Design styles';

    protected static ?string $modelLabel = 'design style';

    protected static ?string $pluralModelLabel = 'design styles';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaintBrush;


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
        return StyleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StylesTable::configure($table);
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
            'index' => ListStyles::route('/'),
            'create' => CreateStyle::route('/create'),
            'edit' => EditStyle::route('/{record}/edit'),
        ];
    }
}
