<?php

namespace App\Filament\Resources\ProjectStatuses;

use App\Filament\Resources\ProjectStatuses\Pages\CreateProjectStatus;
use App\Filament\Resources\ProjectStatuses\Pages\EditProjectStatus;
use App\Filament\Resources\ProjectStatuses\Pages\ListProjectStatuses;
use App\Filament\Resources\ProjectStatuses\Schemas\ProjectStatusForm;
use App\Filament\Resources\ProjectStatuses\Tables\ProjectStatusesTable;
use App\Models\ProjectStatus;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProjectStatusResource extends Resource
{
    protected static ?string $model = ProjectStatus::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Categories';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Project stages';

    protected static ?string $modelLabel = 'project stage';

    protected static ?string $pluralModelLabel = 'project stages';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

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
        return ProjectStatusForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectStatusesTable::configure($table);
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
            'index' => ListProjectStatuses::route('/'),
            'create' => CreateProjectStatus::route('/create'),
            'edit' => EditProjectStatus::route('/{record}/edit'),
        ];
    }
}
