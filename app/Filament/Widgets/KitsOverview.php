<?php

namespace App\Filament\Widgets;

use App\Models\Kit;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

class KitsOverview extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Kits', Kit::query()->count()),
            Stat::make(
                'Pending Cleaning',
                new HtmlString(
                    '<span style="color: oklch(0.828 0.189 84.429);">'.
                    e((string) Kit::query()->where('status', 'pending_cleaning')->count()).
                    '</span>',
                ),
            ),
        ];
    }
}
