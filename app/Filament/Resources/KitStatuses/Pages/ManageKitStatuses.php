<?php

namespace App\Filament\Resources\KitStatuses\Pages;

use App\Filament\Resources\KitStatuses\KitStatusResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageKitStatuses extends ManageRecords
{
    protected static string $resource = KitStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
