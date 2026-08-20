<?php

namespace App\Filament\Resources\RosterPeriods;

use App\Filament\Resources\RosterPeriods\Pages\CreateRosterPeriod;
use App\Filament\Resources\RosterPeriods\Pages\ListRosterPeriods;
use App\Filament\Resources\RosterPeriods\Pages\RosterGrid;
use App\Filament\Resources\RosterPeriods\Schemas\RosterPeriodForm;
use App\Filament\Resources\RosterPeriods\Tables\RosterPeriodsTable;
use App\Models\RosterPeriod;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RosterPeriodResource extends Resource
{
    protected static ?string $model = RosterPeriod::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|\UnitEnum|null $navigationGroup = 'Roster';

    protected static ?string $navigationLabel = 'Roster Periods';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['in_charge', 'area_manager', 'head_nursing', 'admin']);
    }

    public static function canCreate(): bool
    {
        return in_array(auth()->user()?->role, ['in_charge', 'admin']);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        $query = parent::getEloquentQuery()->orderByDesc('period_start');

        // in_charge only sees their own unit
        if ($user->role === 'in_charge') {
            $query->where('unit_id', $user->unit_id);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return RosterPeriodForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RosterPeriodsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRosterPeriods::route('/'),
            'create' => CreateRosterPeriod::route('/create'),
            'grid' => RosterGrid::route('/{record}/grid'),
        ];
    }
}
