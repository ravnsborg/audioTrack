<?php

namespace App\Filament\Widgets;

use App\Models\Dispatch;
use App\Models\Driver;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class TodaysDispatches extends TableWidget
{
    private const RECORDS_PER_PAGE_OPTIONS = [5, 10, 25, 50];

    protected static bool $isLazy = false;

    protected static ?int $sort = 1;

    protected static ?string $heading = 'Today’s Dispatches';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->header(view('filament.dispatches.todays-dispatches-header', [
                'drivers' => $this->getDriverOptions(),
                'recordsPerPageOptions' => self::RECORDS_PER_PAGE_OPTIONS,
            ]))
            ->extraAttributes(['class' => 'dispatches-table-per-page'])
            ->paginationPageOptions(self::RECORDS_PER_PAGE_OPTIONS)
            ->query(fn (): Builder => Dispatch::query()
                ->with(['clientLocation.client', 'kit', 'driver.user'])
                ->whereDate('delivery_datetime', today()))
            ->columns([
                TextColumn::make('clientLocation.title')
                    ->label('Client Location')
                    ->description(fn (Dispatch $record): string => $record->clientLocation->client->name)
                    ->sortable(),
                TextColumn::make('kit.kit_number')
                    ->label('Kit')
                    ->sortable(),
                TextColumn::make('driver.user.name')
                    ->label('Driver')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Dispatch::STATUSES[$state])
                    ->sortable(),
                TextColumn::make('queue_position')
                    ->label('Queue Position')
                    ->numeric(),
            ])
            ->defaultSort('queue_position')
            ->filters([
                SelectFilter::make('driver_id')
                    ->label('Driver')
                    ->options($this->getDriverOptions())
                    ->placeholder('All Drivers'),
            ], FiltersLayout::Hidden)
            ->deferFilters(false);
    }

    /**
     * @return array<int, string>
     */
    private function getDriverOptions(): array
    {
        return Driver::query()
            ->with('user:id,name')
            ->get(['id', 'user_id'])
            ->sortBy(fn (Driver $driver): string => $driver->user->name)
            ->mapWithKeys(fn (Driver $driver): array => [$driver->getKey() => $driver->user->name])
            ->all();
    }
}
