<?php

namespace App\Filament\Resources\OffDayRequests\Pages;

use App\Filament\Resources\OffDayRequests\OffDayRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOffDayRequests extends ListRecords
{
    protected static string $resource = OffDayRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
