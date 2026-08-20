<?php

namespace App\Filament\Resources\RosterPeriods\Pages;

use App\Filament\Resources\RosterPeriods\RosterPeriodResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRosterPeriod extends EditRecord
{
    protected static string $resource = RosterPeriodResource::class;

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
