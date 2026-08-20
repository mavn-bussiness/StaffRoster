<?php

namespace App\Filament\Widgets;

use App\Models\RosterPeriod;
use Filament\Widgets\ChartWidget;

class RosterStatusChart extends ChartWidget
{
    protected ?string $heading = 'Roster Periods by Status';

    protected static ?int $sort = 2;

    public ?string $dataChecksum = null;

    protected function getType(): string
    {
        return 'doughnut';
    }

    public static function canView(): bool
    {
        return in_array(auth()->user()?->role, ['admin', 'area_manager', 'head_nursing']);
    }

    protected function getData(): array
    {
        $statuses = [
            'draft', 'agreed', 'under_review',
            'pending_approval', 'revision_requested', 'approved', 'submitted',
        ];
        $labels = [
            'Draft', 'Agreed', 'Under Review',
            'Pending Approval', 'Revision Requested', 'Approved', 'Submitted',
        ];
        $colors = [
            '#9ca3af', '#60a5fa', '#fbbf24',
            '#fb923c', '#f87171', '#34d399', '#10b981',
        ];

        $counts = RosterPeriod::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return [
            'datasets' => [[
                'data' => collect($statuses)->map(fn ($s) => $counts[$s] ?? 0)->values()->toArray(),
                'backgroundColor' => $colors,
                'borderWidth' => 2,
            ]],
            'labels' => $labels,
        ];
    }
}
