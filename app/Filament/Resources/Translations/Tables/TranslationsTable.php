<?php

namespace App\Filament\Resources\Translations\Tables;

use App\Filament\Support\Translatable;
use App\Models\Translation;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TranslationsTable
{
    public static function configure(Table $table): Table
    {
        $others = collect(Translatable::locales())->reject(fn ($l) => $l->is_default);

        return $table
            ->columns([
                TextColumn::make('source')->label('English')->limit(70)->searchable()->wrap(),
                ...$others->map(fn ($locale) => TextColumn::make("values.{$locale->code}")
                    ->label($locale->name)
                    ->limit(50)
                    ->wrap()
                    ->placeholder('— missing —'))->values()->all(),
                IconColumn::make('is_pattern')->label('Pattern')->boolean()->toggleable(),
                TextColumn::make('group')->label('Group')->badge()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('group')->options(fn () => Translation::query()->distinct()->pluck('group', 'group')),
                // Fastest way to find work: everything still waiting on a language.
                ...$others->map(fn ($locale) => Filter::make("missing_{$locale->code}")
                    ->label("Missing {$locale->name}")
                    ->query(fn ($query) => $query->where(function ($q) use ($locale) {
                        $q->whereNull('values')
                            ->orWhereRaw("json_extract(\"values\", '$.\"{$locale->code}\"') IS NULL")
                            ->orWhereRaw("json_extract(\"values\", '$.\"{$locale->code}\"') = ''");
                    })))->values()->all(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
