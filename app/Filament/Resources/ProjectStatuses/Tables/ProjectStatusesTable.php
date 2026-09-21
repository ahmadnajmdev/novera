<?php

namespace App\Filament\Resources\ProjectStatuses\Tables;

use App\Filament\Support\Translatable;
use App\Models\ProjectStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectStatusesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort')->defaultSort('sort')
            ->columns([
                TextColumn::make('name')->label('Status')
                    ->getStateUsing(fn (ProjectStatus $r) => nv_tr($r, 'name', Translatable::defaultCode())),
                TextColumn::make('key')->label('Key')->badge()->color('gray'),
                TextColumn::make('projects_count')->counts('projects')->label('Projects')->badge(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
