<?php

namespace App\Filament\Resources\RosterPeriods\Pages;

use App\Filament\Resources\RosterPeriods\RosterPeriodResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRosterPeriods extends ListRecords
{
    protected static string $resource = RosterPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
