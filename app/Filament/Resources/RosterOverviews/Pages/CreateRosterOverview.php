<?php

namespace App\Filament\Resources\RosterOverviews\Pages;

use App\Filament\Resources\RosterOverviews\RosterOverviewResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRosterOverview extends CreateRecord
{
    protected static string $resource = RosterOverviewResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
