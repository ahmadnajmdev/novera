<?php

namespace App\Filament\Widgets;

use App\Filament\Support\Translatable;
use App\Models\FormSubmission;
use App\Models\Page;
use App\Models\Project;
use App\Models\Translation;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Four numbers that answer "is anything waiting for me?" — each phrased as a
 * state of the website rather than a table row count.
 */
class SiteOverview extends StatsOverviewWidget
{
    // Rendered with the page, not fetched afterwards: a dashboard that
    // arrives blank and fills in later is the first thing a new editor
    // sees, and it reads as broken.
    protected static bool $isLazy = false;

    protected static ?int $sort = -2;

    protected ?string $heading = 'Where the site stands';

    protected function getStats(): array
    {
        $live = Page::published()->count();
        $drafts = Page::count() - $live;
        $unread = FormSubmission::whereNull('read_at')->count();
        $untranslated = $this->untranslatedCount();

        return [
            Stat::make('Pages live', (string) $live)
                ->description($drafts > 0
                    ? ($drafts === 1 ? '1 more still a draft' : "{$drafts} more still drafts")
                    : 'Nothing waiting in drafts')
                ->color($drafts > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-document-text'),

            Stat::make('Projects published', (string) Project::where('is_active', true)->count())
                ->description('Shown in the portfolio')
                ->color('gray')
                ->icon('heroicon-o-building-office-2'),

            Stat::make('Unread messages', (string) $unread)
                ->description($unread > 0 ? 'Someone is waiting for a reply' : 'All caught up')
                ->color($unread > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-envelope'),

            Stat::make('Phrases missing a translation', (string) $untranslated)
                ->description($untranslated > 0
                    ? 'These fall back to English on the site'
                    : 'Every phrase is translated')
                ->color($untranslated > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-language'),
        ];
    }

    /** Rows where at least one non-default language has no value yet. */
    protected function untranslatedCount(): int
    {
        $codes = collect(Translatable::locales())
            ->reject(fn ($locale) => $locale->is_default)
            ->pluck('code');

        if ($codes->isEmpty()) {
            return 0;
        }

        return Translation::query()
            ->where(function ($query) use ($codes) {
                foreach ($codes as $code) {
                    $query->orWhereNull('values')
                        ->orWhereRaw("json_extract(\"values\", '$.\"{$code}\"') IS NULL")
                        ->orWhereRaw("json_extract(\"values\", '$.\"{$code}\"') = ''");
                }
            })
            ->count();
    }
}
