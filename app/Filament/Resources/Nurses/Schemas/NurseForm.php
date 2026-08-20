<?php

namespace App\Filament\Resources\Nurses\Schemas;

use App\Models\Unit;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NurseForm
{
    public static function configure(Schema $schema): Schema
    {
        $isAdmin = auth()->user()?->role === 'admin';

        $components = [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            TextInput::make('grade')
                ->maxLength(100),
            Select::make('employment_type')
                ->options([
                    'Permanent' => 'Permanent',
                    'Contract' => 'Contract',
                    'Locum' => 'Locum',
                    'Student' => 'Student',
                ]),
            DatePicker::make('date_joined'),
            Select::make('status')
                ->options([
                    'active' => 'Active',
                    'on_leave' => 'On Leave',
                    'inactive' => 'Inactive',
                ])
                ->required()
                ->default('active'),
        ];

        if ($isAdmin) {
            $components[] = Select::make('home_unit_id')
                ->label('Home Unit')
                ->options(fn () => Unit::orderBy('name')->pluck('name', 'id'))
                ->required()
                ->searchable();

            $components[] = Select::make('current_unit_id')
                ->label('Current Unit')
                ->options(fn () => Unit::orderBy('name')->pluck('name', 'id'))
                ->required()
                ->searchable();
        }

        return $schema->components($components);
    }
}
