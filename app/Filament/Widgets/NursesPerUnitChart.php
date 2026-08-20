<?php

namespace App\Filament\Widgets;

use App\Models\Nurse;
use Filament\Widgets\ChartWidget;

class NursesPerUnitChart extends ChartWidget
{
    protected ?string $heading = 'Nurses per Unit';

    public static function canView(): bool
    {
        return in_array(auth()->user()?->role, ['admin', 'area_manager', 'head_nursing']);
    }

    protected function getData(): array
    {
        $nurses = Nurse::with('currentUnit')
            ->get()
            ->groupBy(fn ($nurse) => $nurse->currentUnit?->name ?? 'Unassigned')
            ->map(fn ($group) => $group->count());

        $colors = ['#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#f97316'];

        return [
            'datasets' => [
                [
                    'label' => 'Nurses',
                    'data' => array_values($nurses->toArray()),
                    'backgroundColor' => array_slice($colors, 0, $nurses->count()),
                    'borderColor' => '#fff',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => array_keys($nurses->toArray()),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
