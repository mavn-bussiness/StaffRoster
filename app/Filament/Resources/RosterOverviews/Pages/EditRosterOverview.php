<?php

namespace App\Filament\Resources\RosterOverviews\Pages;

use App\Filament\Resources\RosterOverviews\RosterOverviewResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRosterOverview extends EditRecord
{
    protected static string $resource = RosterOverviewResource::class;

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
