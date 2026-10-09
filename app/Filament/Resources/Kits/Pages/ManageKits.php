<?php

namespace App\Filament\Resources\Kits\Pages;

use App\Filament\Resources\Kits\KitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageKits extends ManageRecords
{
    protected static string $resource = KitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
