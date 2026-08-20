<?php

namespace App\Filament\Resources\Transfers\Pages;

use App\Filament\Resources\Transfers\TransferResource;
use App\Models\Nurse;
use App\Models\Unit;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTransfer extends CreateRecord
{
    protected static string $resource = TransferResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $nurse = Nurse::findOrFail($data['nurse_id']);
        $toUnit = Unit::findOrFail($data['to_unit_id']);

        if (auth()->user()->role === 'in_charge') {
            abort_unless($nurse->current_unit_id === auth()->user()->unit_id, 403);
        }

        return $nurse->transferTo($toUnit, $data['start_date'], $data['end_date'] ?? null, $data['reason'] ?? null);
    }

    protected function getRedirectUrl(): string
    {
        return TransferResource::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Transfer request submitted. Awaiting Head Nursing approval.')
            ->success();
    }
}
