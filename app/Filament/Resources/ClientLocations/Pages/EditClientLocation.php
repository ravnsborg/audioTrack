<?php

namespace App\Filament\Resources\ClientLocations\Pages;

use App\Filament\Resources\ClientLocations\ClientLocationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClientLocation extends EditRecord
{
    protected static string $resource = ClientLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
