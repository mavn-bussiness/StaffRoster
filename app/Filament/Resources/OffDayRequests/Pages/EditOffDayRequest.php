<?php

namespace App\Filament\Resources\OffDayRequests\Pages;

use App\Filament\Resources\OffDayRequests\OffDayRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOffDayRequest extends EditRecord
{
    protected static string $resource = OffDayRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
