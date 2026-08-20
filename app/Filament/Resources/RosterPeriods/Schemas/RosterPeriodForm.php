<?php

namespace App\Filament\Resources\RosterPeriods\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RosterPeriodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('roster_month')
                    ->required()
                    ->label('Roster Month')
                    ->displayFormat('F Y')
                    ->format('Y-m')
                    ->minDate(Carbon::now()->addMonth()->startOfMonth())
                    ->maxDate(Carbon::now()->addMonth()->endOfMonth())
                    ->default(Carbon::now()->addMonth()->startOfMonth())
                    ->helperText('Rosters can only be created for the upcoming month.'),
                TextInput::make('minimum_duty_days')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(20)
                    ->label('Minimum Duty Days'),
                DatePicker::make('ward_meeting_date')
                    ->label('Ward Meeting Date')
                    ->helperText('Monthly ward unit meeting date for this roster period.'),
            ]);
    }
}
