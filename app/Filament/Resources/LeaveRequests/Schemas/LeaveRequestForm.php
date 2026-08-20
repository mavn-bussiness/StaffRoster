<?php

namespace App\Filament\Resources\LeaveRequests\Schemas;

use App\Models\Nurse;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class LeaveRequestForm
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
                Select::make('type')
                    ->options([
                        'annual' => 'Annual',
                        'sick' => 'Sick',
                        'maternity' => 'Maternity',
                        'compassionate' => 'Compassionate',
                        'other' => 'Other',
                    ])
                    ->required(),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date')
                    ->required()
                    ->afterOrEqual('start_date'),
                DatePicker::make('return_date')
                    ->afterOrEqual('end_date'),
            ]);
    }
}
