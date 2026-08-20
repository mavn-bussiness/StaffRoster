<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Unit;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    private const UNIT_SCOPED = [User::ROLE_IN_CHARGE, User::ROLE_AREA_MANAGER];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('role')
                    ->options([
                        User::ROLE_ADMIN => 'Admin',
                        User::ROLE_IN_CHARGE => 'In-Charge',
                        User::ROLE_AREA_MANAGER => 'Area Manager',
                        User::ROLE_HEAD_NURSING => 'Head Nursing',
                    ])
                    ->required()
                    ->default(User::ROLE_IN_CHARGE)
                    ->live(),
                Select::make('unit_id')
                    ->label('Unit')
                    ->options(Unit::orderBy('name')->pluck('name', 'id'))
                    ->visible(fn ($get) => in_array($get('role'), self::UNIT_SCOPED))
                    ->required(fn ($get) => in_array($get('role'), self::UNIT_SCOPED)),
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn ($record) => $record === null)
                    ->minLength(8),
            ]);
    }
}
