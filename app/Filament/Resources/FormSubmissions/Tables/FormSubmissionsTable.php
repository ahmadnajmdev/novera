<?php

namespace App\Filament\Resources\FormSubmissions\Tables;

use App\Models\FormSubmission;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FormSubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Received')->dateTime('j M Y, H:i')->sortable(),
                TextColumn::make('form.name')->label('Form')->badge(),
                TextColumn::make('summary')->label('From')
                    ->getStateUsing(fn (FormSubmission $r) => collect($r->payload)->only(['name', 'email', 'phone'])->filter()->implode(' · '))
                    ->wrap(),
                TextColumn::make('locale')->label('Language')->badge()->color('gray'),
                IconColumn::make('read_at')->label('Read')->boolean(),
            ])
            ->filters([
                SelectFilter::make('form_id')->label('Form')->relationship('form', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('toggleRead')
                    ->label(fn (FormSubmission $r) => $r->read_at ? 'Mark unread' : 'Mark read')
                    ->icon('heroicon-o-check-circle')
                    ->action(fn (FormSubmission $r) => $r->update(['read_at' => $r->read_at ? null : now()])),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
