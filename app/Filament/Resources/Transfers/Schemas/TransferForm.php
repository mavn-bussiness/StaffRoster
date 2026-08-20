<?php

namespace App\Filament\Resources\Transfers\Schemas;

use App\Models\Nurse;
use App\Models\Unit;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('nurse_id')
                    ->label('Nurse')
                    ->options(function () {
                        $user = auth()->user();
                        $query = Nurse::orderBy('name');
                        if ($user->role === 'in_charge') {
                            $query->where('current_unit_id', $user->unit_id);
                        }

                        return $query->pluck('name', 'id');
                    })
                    ->required()
                    ->searchable(),
                Select::make('to_unit_id')
                    ->label('Transfer To')
                    ->options(function () {
                        $user = auth()->user();
                        $query = Unit::orderBy('name');
                        if ($user->role === 'in_charge') {
                            $query->where('id', '!=', $user->unit_id);
                        }

                        return $query->pluck('name', 'id');
                    })
                    ->required(),
                DatePicker::make('start_date')->required(),
                DatePicker::make('end_date')->afterOrEqual('start_date'),
                TextInput::make('reason')->maxLength(255),
            ]);
    }
}
