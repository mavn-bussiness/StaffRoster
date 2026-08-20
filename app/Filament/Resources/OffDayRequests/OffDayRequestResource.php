<?php

namespace App\Filament\Resources\OffDayRequests;

use App\Filament\Resources\OffDayRequests\Pages\CreateOffDayRequest;
use App\Filament\Resources\OffDayRequests\Pages\ListOffDayRequests;
use App\Filament\Resources\OffDayRequests\Schemas\OffDayRequestForm;
use App\Filament\Resources\OffDayRequests\Tables\OffDayRequestsTable;
use App\Models\OffDayRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OffDayRequestResource extends Resource
{
    protected static ?string $model = OffDayRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNoSymbol;

    protected static string|\UnitEnum|null $navigationGroup = 'Roster';

    protected static ?string $navigationLabel = 'Off-Day Requests';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['in_charge', 'admin', 'area_manager', 'head_nursing']);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery()->with('nurse', 'rosterPeriod')->orderByDesc('requested_date');

        if ($user?->role === 'in_charge') {
            $query->whereHas('nurse', fn ($q) => $q->where('current_unit_id', $user->unit_id));
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return OffDayRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OffDayRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOffDayRequests::route('/'),
            'create' => CreateOffDayRequest::route('/create'),
        ];
    }
}
