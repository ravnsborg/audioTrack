<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected bool $isDriver = false;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['is_driver'] = $this->getRecord()->driver()->exists();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->isDriver = (bool) ($data['is_driver'] ?? false);
        unset($data['is_driver']);

        return $data;
    }

    protected function afterSave(): void
    {
        $driver = $this->getRecord()->driver();

        if ($this->isDriver) {
            $driver->firstOrCreate([], [
                'status' => 'active',
            ]);

            return;
        }

        $driver->delete();
    }
}
