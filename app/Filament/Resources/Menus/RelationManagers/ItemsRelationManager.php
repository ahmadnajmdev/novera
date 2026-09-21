<?php

namespace App\Filament\Resources\Menus\RelationManagers;

use App\Filament\Support\Translatable;
use App\Models\MenuItem;
use App\Models\Page;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'allItems';

    protected static ?string $title = 'Links';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Translatable::tabs(fn (string $locale, bool $isDefault) => [
                TextInput::make("label.{$locale}")->label('Label')->required($isDefault),
            ]),
            Section::make('Destination')
                ->description('Pick a page so the link follows its slug in every language, or enter a URL.')
                ->schema([
                    Select::make('page_id')
                        ->label('Page')
                        ->options(fn () => Page::all()->mapWithKeys(fn (Page $p) => [$p->id => nv_tr($p, 'title', Translatable::defaultCode())]))
                        ->searchable()->native(false),
                    TextInput::make('url')->label('Or a URL')->maxLength(2048),
                    Select::make('target')->label('Open in')
                        ->options(['_self' => 'Same tab', '_blank' => 'New tab'])->default('_self')->native(false),
                    TextInput::make('sort')->label('Order')->numeric()->default(0),
                    Toggle::make('is_visible')->label('Visible')->default(true),
                ])->columns(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('label')->label('Label')
                    ->getStateUsing(fn (MenuItem $r) => nv_tr($r, 'label', Translatable::defaultCode())),
                TextColumn::make('destination')->label('Goes to')
                    ->getStateUsing(fn (MenuItem $r) => $r->page ? nv_tr($r->page, 'title', Translatable::defaultCode()) : ($r->url ?: '—'))
                    ->color('gray'),
                IconColumn::make('is_visible')->label('Visible')->boolean(),
            ])
            ->headerActions([CreateAction::make()->label('Add link')])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
