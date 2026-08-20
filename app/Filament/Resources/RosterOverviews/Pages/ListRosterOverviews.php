<?php

namespace App\Filament\Resources\RosterOverviews\Pages;

use App\Filament\Resources\RosterOverviews\RosterOverviewResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRosterOverviews extends ListRecords
{
    protected static string $resource = RosterOverviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
