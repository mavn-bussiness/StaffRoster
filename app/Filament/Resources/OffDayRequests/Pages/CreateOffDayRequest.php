<?php

namespace App\Filament\Resources\OffDayRequests\Pages;

use App\Filament\Resources\OffDayRequests\OffDayRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOffDayRequest extends CreateRecord
{
    protected static string $resource = OffDayRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'requested';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
