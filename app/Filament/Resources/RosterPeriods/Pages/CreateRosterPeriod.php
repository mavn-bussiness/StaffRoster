<?php

namespace App\Filament\Resources\RosterPeriods\Pages;

use App\Filament\Resources\RosterPeriods\RosterPeriodResource;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;

class CreateRosterPeriod extends CreateRecord
{
    protected static string $resource = RosterPeriodResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $month = Carbon::parse($data['roster_month']);

        $data['period_start'] = $month->startOfMonth()->toDateString();
        $data['period_end'] = $month->copy()->endOfMonth()->toDateString();
        $data['unit_id'] = auth()->user()->unit_id;
        $data['created_by'] = auth()->id();
        $data['status'] = 'draft';

        unset($data['roster_month']);

        if (! empty($data['ward_meeting_date'])) {
            $meetingDate = Carbon::parse($data['ward_meeting_date']);
            $start = Carbon::parse($data['period_start']);
            $end = Carbon::parse($data['period_end']);

            if ($meetingDate->lt($start) || $meetingDate->gt($end)) {
                Notification::make()
                    ->title('Invalid ward meeting date')
                    ->body('The ward meeting date must fall within the selected roster month.')
                    ->danger()
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        try {
            $record = parent::handleRecordCreation($data);
            $record->unit?->syncInChargeNurse();

            return $record;
        } catch (UniqueConstraintViolationException) {
            $month = Carbon::parse($data['period_start'])->format('F Y');

            Notification::make()
                ->title('Roster already exists')
                ->body("A roster period for {$month} already exists for your unit.")
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }
    }

    protected function getRedirectUrl(): string
    {
        return RosterPeriodResource::getUrl('grid', ['record' => $this->record]);
    }
}
