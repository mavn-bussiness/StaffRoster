<?php

namespace App\Filament\Widgets;

use App\Models\Shift;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ShiftCoverageChart extends ChartWidget
{
    protected ?string $heading = 'Shift Coverage (Next 30 Days)';

    public static function canView(): bool
    {
        return in_array(auth()->user()?->role, ['admin', 'area_manager', 'head_nursing']);
    }

    protected function getData(): array
    {
        $dates = [];
        $dayShifts = [];
        $eveningShifts = [];
        $nightShifts = [];

        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addDays(30);

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dates[] = $date->format('M d');

            $dayCount = Shift::whereDate('shift_date', $date)->where('code', 'D')->count();
            $eveningCount = Shift::whereDate('shift_date', $date)->where('code', 'E')->count();
            $nightCount = Shift::whereDate('shift_date', $date)->where('code', 'N')->count();

            $dayShifts[] = $dayCount;
            $eveningShifts[] = $eveningCount;
            $nightShifts[] = $nightCount;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Day Shifts',
                    'data' => $dayShifts,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4,
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Evening Shifts',
                    'data' => $eveningShifts,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'tension' => 0.4,
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Night Shifts',
                    'data' => $nightShifts,
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'tension' => 0.4,
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $dates,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
