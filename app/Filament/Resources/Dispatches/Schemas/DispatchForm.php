<?php

namespace App\Filament\Resources\Dispatches\Schemas;

use App\Models\ClientLocation;
use App\Models\Dispatch;
use App\Models\Driver;
use App\Models\Kit;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DispatchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('driver_id')
                    ->label('Driver')
                    ->options(fn (): array => Driver::query()
                        ->with('user')
                        ->get()
                        ->mapWithKeys(fn (Driver $driver): array => [
                            $driver->getKey() => $driver->user->name,
                        ])
                        ->all())
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('client_location_id')
                    ->label('Client location')
                    ->options(fn (): array => ClientLocation::query()
                        ->with('client')
                        ->get()
                        ->mapWithKeys(fn (ClientLocation $location): array => [
                            $location->getKey() => "{$location->client->name} - {$location->title}",
                        ])
                        ->all())
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('kit_id')
                    ->label('Kit')
                    ->options(fn (): array => Kit::query()
                        ->orderBy('kit_number')
                        ->pluck('kit_number', 'id')
                        ->all())
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('status')
                    ->options(Dispatch::STATUSES)
                    ->default('staged')
                    ->required(),
                TextInput::make('queue_position')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),
                DateTimePicker::make('delivery_datetime')
                    ->seconds(false),
            ]);
    }
}
