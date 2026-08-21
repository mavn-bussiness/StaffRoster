<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Nurse;
use App\Models\RosterPeriod;
use Carbon\CarbonPeriod;
use Illuminate\View\View;

class NurseRosterController extends Controller
{
    public function show(Nurse $nurse): View
    {
        $periods = RosterPeriod::where('unit_id', $nurse->current_unit_id)
            ->whereIn('status', ['agreed', 'approved', 'submitted'])
            ->with(['shifts' => fn ($q) => $q->where('nurse_id', $nurse->id)])
            ->orderByDesc('period_start')
            ->take(3)
            ->get();

        $periodsData = $periods->map(function ($period) use ($nurse) {
            $dates = CarbonPeriod::create($period->period_start, $period->period_end);

            $byNurse = $period->shifts->groupBy('nurse_id')
                ->map(fn ($shifts) => $shifts->keyBy(fn ($s) => $s->shift_date->toDateString()));

            $leaveSegments = LeaveRequest::where('status', '!=', 'rejected')
                ->where('nurse_id', $nurse->id)
                ->where('start_date', '<=', $period->period_end)
                ->where('end_date', '>=', $period->period_start)
                ->get()
                ->map(fn ($leave) => [
                    'start' => $leave->start_date->max($period->period_start),
                    'end' => $leave->end_date->min($period->period_end),
                    'type' => $leave->type,
                ])
                ->values()
                ->all();

            return [
                'period' => $period,
                'dates' => $dates,
                'byNurse' => $byNurse,
                'leaveSegments' => $leaveSegments,
            ];
        });

        return view('nurse.roster', compact('nurse', 'periodsData'));
    }
}
