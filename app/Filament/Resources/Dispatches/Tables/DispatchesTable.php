<?php

namespace App\Filament\Resources\Dispatches\Tables;

use App\Models\Dispatch;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DispatchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('driver.user.name')
                    ->label('Driver')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('clientLocation.title')
                    ->label('Client location')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kit.kit_number')
                    ->label('Kit')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Dispatch::STATUSES[$state])
                    ->searchable()
                    ->sortable(),
                TextColumn::make('queue_position')
                    ->label('Stops before delivery')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('delivery_datetime')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not scheduled'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(Dispatch::STATUSES)
                    ->placeholder('All statuses'),
            ], FiltersLayout::Hidden)
            ->deferFilters(false)
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
