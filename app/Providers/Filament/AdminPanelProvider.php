<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\RecentMessages;
use App\Filament\Widgets\SiteOverview;
use App\Filament\Widgets\StartHere;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\View\PanelsRenderHook;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Novera CMS')
            ->colors([
                'primary' => Color::Amber,
                'gray' => Color::Slate,
            ])
            // Plain-language groups in the order an editor works: the day-to-day
            // screens stay open, the rarely-touched ones start collapsed so the
            // sidebar reads as a short list rather than a wall of twenty links.
            ->navigationGroups([
                NavigationGroup::make('Website')
                    ->icon(Heroicon::OutlinedComputerDesktop),
                NavigationGroup::make('Media & lists')
                    ->icon(Heroicon::OutlinedPhoto),
                NavigationGroup::make('Menus & forms')
                    ->icon(Heroicon::OutlinedBars3),
                NavigationGroup::make('Categories')
                    ->icon(Heroicon::OutlinedTag)
                    ->collapsed(),
                NavigationGroup::make('Languages')
                    ->icon(Heroicon::OutlinedLanguage)
                    ->collapsed(),
                NavigationGroup::make('Settings')
                    ->icon(Heroicon::OutlinedCog6Tooth)
                    ->collapsed(),
                NavigationGroup::make('Advanced')
                    ->icon(Heroicon::OutlinedWrenchScrewdriver)
                    ->collapsed(),
            ])
            ->sidebarCollapsibleOnDesktop()
            // In <head> rather than pushed from each view: a @push inside a
            // component never reaches the layout when Livewire renders it
            // into a modal, which left the section picker unstyled.
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => view('filament.admin-styles'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                StartHere::class,
                SiteOverview::class,
                RecentMessages::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
