<?php

namespace App\Providers\Filament;

use App\Filament\Resources\ClientLocations\Pages\ListClientLocations;
use App\Filament\Resources\Items\Pages\ManageItems;
use App\Filament\Resources\Kits\Pages\ManageKits;
use App\Models\Category;
use App\Models\Client;
use App\Models\KitStatus;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Tables\View\TablesRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Contracts\View\View;
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
            ->brandName('AudioTrack')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->renderHook(
                TablesRenderHook::TOOLBAR_GROUPING_SELECTOR_BEFORE,
                function (): View|string {
                    $currentPage = app('livewire')->current();

                    if ($currentPage instanceof ManageItems) {
                        return view('filament.items.category-filter', [
                            'categories' => Category::query()->orderBy('name')->pluck('name', 'id'),
                        ]);
                    }

                    if ($currentPage instanceof ManageKits) {
                        return view('filament.kits.status-filter', [
                            'statuses' => KitStatus::query()->orderBy('name')->pluck('name', 'id'),
                        ]);
                    }

                    if ($currentPage instanceof ListClientLocations) {
                        return view('filament.client-locations.client-filter', [
                            'clients' => Client::query()->orderBy('name')->pluck('name', 'id'),
                        ]);
                    }

                    return '';
                },
            )
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
