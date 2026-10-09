<?php

namespace App\Filament\Widgets;

use App\Models\Kit;
use App\Models\KitStatus;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

class KitsOverview extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $statusCounts = KitStatus::query()
            ->whereIn('name', ['active', 'in_transit', 'pending_cleaning'])
            ->withCount('kits')
            ->pluck('kits_count', 'name');

        return [
            Stat::make('Total Kits', Kit::query()->count()),
            Stat::make(
                'Active Kits',
                new HtmlString('<span style="color: #86efac;">'.e((string) $statusCounts->get('active', 0)).'</span>'),
            )->color('success'),
            Stat::make(
                'In Transit Kits',
                new HtmlString('<span style="color: oklch(0.51 0.26 276.81);">'.e((string) $statusCounts->get('in_transit', 0)).'</span>'),
            ),
            Stat::make(
                'Pending Cleaning',
                new HtmlString('<span style="color: #fbbf24;">'.e((string) $statusCounts->get('pending_cleaning', 0)).'</span>'),
            )->color('warning'),
        ];
    }
}
