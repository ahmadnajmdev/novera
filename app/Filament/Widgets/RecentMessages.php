<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\FormSubmissions\FormSubmissionResource;
use App\Models\FormSubmission;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

/**
 * The last handful of contact-form enquiries, on the first screen, so nobody
 * has to know that "form submissions" is where their customers ended up.
 */
class RecentMessages extends TableWidget
{
    protected static ?int $sort = -1;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Latest enquiries';

    public static function canView(): bool
    {
        return FormSubmission::exists();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(FormSubmission::query()->with('form')->latest())
            ->paginated(false)
            ->modifyQueryUsing(fn ($query) => $query->limit(5))
            ->columns([
                TextColumn::make('created_at')->label('Received')->since()->tooltip(
                    fn (FormSubmission $record) => $record->created_at?->format('j M Y, H:i'),
                ),
                TextColumn::make('summary')
                    ->label('From')
                    ->getStateUsing(fn (FormSubmission $record) => collect($record->payload)
                        ->only(['name', 'email', 'phone'])->filter()->implode(' · ') ?: '—')
                    ->wrap(),
                TextColumn::make('read_at')
                    ->label('')
                    ->badge()
                    ->getStateUsing(fn (FormSubmission $record) => $record->read_at ? null : 'New')
                    ->color('danger'),
            ])
            ->recordActions([
                Action::make('open')
                    ->label('Read')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn (FormSubmission $record) => FormSubmissionResource::getUrl('edit', [
                        'record' => $record,
                    ])),
            ])
            ->emptyStateHeading('No enquiries yet');
    }
}
