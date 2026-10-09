<?php

namespace App\Filament\Resources\ItemStatuses\Pages;

use App\Filament\Resources\ItemStatuses\ItemStatusResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageItemStatuses extends ManageRecords
{
    protected static string $resource = ItemStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
