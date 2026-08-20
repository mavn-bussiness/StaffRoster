<?php

namespace App\Filament\Resources\Nurses;

use App\Filament\Resources\Nurses\Pages\CreateNurse;
use App\Filament\Resources\Nurses\Pages\EditNurse;
use App\Filament\Resources\Nurses\Pages\ListNurses;
use App\Filament\Resources\Nurses\Schemas\NurseForm;
use App\Filament\Resources\Nurses\Tables\NursesTable;
use App\Models\Nurse;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NurseResource extends Resource
{
    protected static ?string $model = Nurse::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|\UnitEnum|null $navigationGroup = 'Unit Management';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['in_charge', 'admin', 'area_manager', 'head_nursing']);
    }

    public static function canCreate(): bool
    {
        return in_array(auth()->user()?->role, ['in_charge', 'admin']);
    }

    public static function canEdit($record): bool
    {
        return in_array(auth()->user()?->role, ['in_charge', 'admin']);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery();

        if ($user?->role === 'in_charge') {
            $query->where('current_unit_id', $user->unit_id);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return NurseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NursesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNurses::route('/'),
            'create' => CreateNurse::route('/create'),
            'edit' => EditNurse::route('/{record}/edit'),
        ];
    }
}
