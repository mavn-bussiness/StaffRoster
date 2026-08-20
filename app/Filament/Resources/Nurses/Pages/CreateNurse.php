<?php

namespace App\Filament\Resources\Nurses\Pages;

use App\Filament\Resources\Nurses\NurseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNurse extends CreateRecord
{
    protected static string $resource = NurseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if ($user->role === 'in_charge') {
            $data['home_unit_id'] = $user->unit_id;
            $data['current_unit_id'] = $user->unit_id;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
