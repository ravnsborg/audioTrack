<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected bool $isDriver = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->isDriver = (bool) ($data['is_driver'] ?? false);
        unset($data['is_driver']);

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->isDriver) {
            $this->getRecord()->driver()->create([
                'status' => 'active',
            ]);
        }
    }
}
