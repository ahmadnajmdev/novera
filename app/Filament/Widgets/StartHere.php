<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\ManageSettings;
use App\Filament\Resources\FormSubmissions\FormSubmissionResource;
use App\Filament\Resources\Media\MediaResource;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Pages\VisualEditor;
use App\Models\FormSubmission;
use Filament\Widgets\Widget;

/**
 * The dashboard an editor lands on.
 *
 * Filament's default dashboard is a blank slate, which leaves someone who has
 * never seen a CMS guessing which of twenty sidebar links does what. This
 * turns the first screen into a short list of jobs, phrased as things people
 * actually want to do.
 */
class StartHere extends Widget
{
    protected string $view = 'filament.widgets.start-here';

    protected int|string|array $columnSpan = 'full';

    // Rendered with the page, not fetched afterwards: a dashboard that
    // arrives blank and fills in later is the first thing a new editor
    // sees, and it reads as broken.
    protected static bool $isLazy = false;

    protected static ?int $sort = -3;

    /** @return array<int, array<string, mixed>> */
    public function getCards(): array
    {
        $cards = [
            [
                'title' => 'Change words and pictures on a page',
                'body' => 'Open the site, click any text, and type over it. The safest place to start.',
                'action' => 'Open the visual editor',
                'icon' => 'heroicon-o-cursor-arrow-rays',
                'url' => VisualEditor::getUrl(),
                'primary' => true,
            ],
            [
                'title' => 'Add or rearrange a page',
                'body' => 'Create pages, set their address, and stack the sections that make them up.',
                'action' => 'Go to pages',
                'icon' => 'heroicon-o-document-text',
                'url' => PageResource::getUrl(),
            ],
            [
                'title' => 'Publish a project',
                'body' => 'Add a completed job with its photos so it shows up in the portfolio.',
                'action' => 'Go to projects',
                'icon' => 'heroicon-o-building-office-2',
                'url' => ProjectResource::getUrl(),
            ],
            [
                'title' => 'Upload photos',
                'body' => 'Everything you can place on the site lives in one library.',
                'action' => 'Go to photos',
                'icon' => 'heroicon-o-photo',
                'url' => MediaResource::getUrl(),
            ],
        ];

        $unread = FormSubmission::whereNull('read_at')->count();

        $cards[] = [
            'title' => 'Read enquiries',
            'body' => $unread > 0
                ? ($unread === 1 ? 'One new message is waiting.' : "{$unread} new messages are waiting.")
                : 'Messages sent through the contact form arrive here.',
            'action' => 'Go to messages',
            'icon' => 'heroicon-o-envelope',
            'url' => FormSubmissionResource::getUrl(),
            'badge' => $unread > 0 ? (string) $unread : null,
        ];

        if (ManageSettings::canAccess()) {
            $cards[] = [
                'title' => 'Phone, address and branding',
                'body' => 'The details that appear in the header, footer and contact page.',
                'action' => 'Go to site settings',
                'icon' => 'heroicon-o-cog-6-tooth',
                'url' => ManageSettings::getUrl(),
            ];
        }

        return $cards;
    }

    public function getGreeting(): string
    {
        $name = str(auth()->user()?->name ?? '')->before(' ')->toString();
        $hour = now()->hour;

        $part = match (true) {
            $hour < 12 => 'Good morning',
            $hour < 18 => 'Good afternoon',
            default => 'Good evening',
        };

        return $name ? "{$part}, {$name}" : $part;
    }
}
