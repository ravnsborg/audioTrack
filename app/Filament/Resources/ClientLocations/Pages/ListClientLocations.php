<?php

namespace App\Filament\Resources\ClientLocations\Pages;

use App\Filament\Resources\ClientLocations\ClientLocationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClientLocations extends ListRecords
{
    protected static string $resource = ClientLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
