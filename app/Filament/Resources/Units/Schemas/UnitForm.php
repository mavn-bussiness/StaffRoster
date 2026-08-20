<?php

namespace App\Filament\Resources\Units\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true)
                    ->afterStateUpdated(fn ($state, $set) => $set('code', strtoupper($state)))
                    ->live(onBlur: true),
                Toggle::make('has_evening_shift')
                    ->default(true),
                Toggle::make('has_night_shift')
                    ->default(true),
            ]);
    }
}
