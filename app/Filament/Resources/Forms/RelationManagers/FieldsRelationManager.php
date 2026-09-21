<?php

namespace App\Filament\Resources\Forms\RelationManagers;

use App\Filament\Support\Translatable;
use App\Models\FormField;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FieldsRelationManager extends RelationManager
{
    protected static string $relationship = 'allFields';

    protected static ?string $title = 'Fields';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Translatable::tabs(fn (string $locale, bool $isDefault) => [
                TextInput::make("label.{$locale}")->label('Label')->required($isDefault),
                TextInput::make("placeholder.{$locale}")->label('Placeholder'),
            ]),

            Section::make('Behaviour')->schema([
                TextInput::make('key')->label('Field key')->required()
                    ->rule('regex:/^[a-z0-9_]+$/')
                    ->helperText('Lowercase, underscores. Used as the column name in submissions.'),
                Select::make('type')->label('Type')->required()->live()->native(false)
                    ->options([
                        'text' => 'Single line',
                        'email' => 'Email',
                        'textarea' => 'Paragraph',
                        'select' => 'Dropdown',
                    ]),
                TextInput::make('rows')->label('Rows')->numeric()
                    ->visible(fn (Get $get) => $get('type') === 'textarea'),
                TextInput::make('rules')->label('Extra validation')
                    ->helperText('Pipe-separated Laravel rules, e.g. min:3|max:80.'),
                TextInput::make('sort')->label('Order')->numeric()->default(0),
                Toggle::make('is_required')->label('Required'),
                Toggle::make('is_visible')->label('Visible')->default(true),
            ])->columns(2),

            Repeater::make('options')
                ->label('Dropdown options')
                ->visible(fn (Get $get) => $get('type') === 'select')
                ->schema([
                    TextInput::make('value')->label('Stored value')->required(),
                    Translatable::tabs(fn (string $locale, bool $isDefault) => [
                        TextInput::make("label.{$locale}")->label('Shown as')->required($isDefault),
                    ], 'Option label'),
                ])
                ->defaultItems(0)
                ->reorderable()
                ->collapsible()
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('key')
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('label')->label('Label')
                    ->getStateUsing(fn (FormField $r) => nv_tr($r, 'label', Translatable::defaultCode())),
                TextColumn::make('key')->label('Key')->badge()->color('gray'),
                TextColumn::make('type')->label('Type')->badge(),
                IconColumn::make('is_required')->label('Required')->boolean(),
                IconColumn::make('is_visible')->label('Visible')->boolean(),
            ])
            ->headerActions([CreateAction::make()->label('Add field')])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
