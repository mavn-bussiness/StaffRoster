<?php

namespace App\Filament\Widgets;

use App\Models\Nurse;
use App\Models\RosterPeriod;
use App\Models\Shift;
use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class DashboardStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $isInCharge = $user?->role === 'in_charge';

        $nursesQuery = Nurse::query();
        $pendingRostersQuery = RosterPeriod::whereIn('status', ['draft', 'agreed']);
        $onDutyQuery = Shift::whereDate('shift_date', Carbon::today())->where('code', '!=', 'O');

        if ($isInCharge) {
            $nursesQuery->where('current_unit_id', $user->unit_id);
            $pendingRostersQuery->where('unit_id', $user->unit_id);
            $onDutyQuery->whereHas('nurse', fn ($q) => $q->where('current_unit_id', $user->unit_id));
        }

        $stats = [
            Stat::make($isInCharge ? 'Unit Nurses' : 'Total Nurses', $nursesQuery->count())
                ->description($isInCharge ? 'In your unit' : 'Active staff members')
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Pending Rosters', $pendingRostersQuery->count())
                ->description('Awaiting approval')
                ->icon('heroicon-o-document')
                ->color('warning'),

            Stat::make('On Duty Today', $onDutyQuery->count())
                ->description('Staff working today')
                ->icon('heroicon-o-clock')
                ->color('success'),
        ];

        if (! $isInCharge) {
            array_splice($stats, 1, 0, [
                Stat::make('Units', Unit::count())
                    ->description('Departments')
                    ->icon('heroicon-o-building-office-2')
                    ->color('info'),
            ]);
        }

        return $stats;
    }
}
