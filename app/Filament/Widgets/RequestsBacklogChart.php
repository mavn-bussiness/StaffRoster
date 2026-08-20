<?php

namespace App\Filament\Widgets;

use App\Models\LeaveRequest;
use App\Models\OffDayRequest;
use Filament\Widgets\ChartWidget;

class RequestsBacklogChart extends ChartWidget
{
    protected ?string $heading = 'Leave & Off-Day Requests Backlog';

    public static function canView(): bool
    {
        return in_array(auth()->user()?->role, ['admin', 'area_manager', 'head_nursing']);
    }

    protected function getData(): array
    {
        $leaveStatuses = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
        $offDayStatuses = ['pending' => 0, 'approved' => 0, 'rejected' => 0];

        foreach ($leaveStatuses as $status => &$count) {
            $count = LeaveRequest::where('status', $status)->count();
        }

        foreach ($offDayStatuses as $status => &$count) {
            $count = OffDayRequest::where('status', $status)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Leave Requests',
                    'data' => array_values($leaveStatuses),
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#1d4ed8',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Off-Day Requests',
                    'data' => array_values($offDayStatuses),
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#059669',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Pending', 'Approved', 'Rejected'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
