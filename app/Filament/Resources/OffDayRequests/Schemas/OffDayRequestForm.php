<?php

namespace App\Filament\Resources\OffDayRequests\Schemas;

use App\Models\Nurse;
use App\Models\RosterPeriod;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OffDayRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('nurse_id')
                    ->label('Nurse')
                    ->options(fn () => Nurse::where('current_unit_id', auth()->user()->unit_id)
                        ->orderBy('name')->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                Select::make('roster_period_id')
                    ->label('Roster Period')
                    ->options(fn () => RosterPeriod::where('unit_id', auth()->user()->unit_id)
                        ->whereIn('status', ['draft', 'agreed'])
                        ->orderByDesc('period_start')
                        ->get()
                        ->mapWithKeys(fn ($p) => [
                            $p->id => $p->period_start->format('d M Y').' – '.$p->period_end->format('d M Y'),
                        ]))
                    ->required(),
                DatePicker::make('requested_date')
                    ->required(),
                TextInput::make('reason')
                    ->maxLength(255),
                DatePicker::make('written_request_date'),
            ]);
    }
}
