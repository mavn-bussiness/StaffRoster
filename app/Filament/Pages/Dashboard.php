<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardStatsOverview;
use App\Filament\Widgets\NursesPerUnitChart;
use App\Filament\Widgets\RequestsBacklogChart;
use App\Filament\Widgets\RosterStatusChart;
use App\Filament\Widgets\ShiftCoverageChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = null;

    public function getView(): string
    {
        return 'filament.pages.dashboard';
    }

    public function getWidgets(): array
    {
        $role = auth()->user()?->role;

        if ($role === 'in_charge') {
            return [
                DashboardStatsOverview::class,
            ];
        }

        return [
            DashboardStatsOverview::class,
            RosterStatusChart::class,
            RequestsBacklogChart::class,
            ShiftCoverageChart::class,
            NursesPerUnitChart::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 2;
    }
}
