<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Nurse;
use App\Models\RosterPeriod;
use App\Models\Unit;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicRosterController extends Controller
{
    public function index(): View
    {
        $units = Unit::orderBy('name')->get();

        return view('public.roster', compact('units'));
    }

    public function unit(Unit $unit): View
    {
        $units = Unit::orderBy('name')->get();

        $unit->syncInChargeNurse();

        $periods = RosterPeriod::where('unit_id', $unit->id)
            ->whereIn('status', ['agreed', 'approved', 'submitted'])
            ->with(['shifts.nurse'])
            ->orderByDesc('period_start')
            ->take(3)
            ->get();

        $periodsData = $periods->map(function ($period) use ($unit) {
            $dates = CarbonPeriod::create($period->period_start, $period->period_end);

            // Get all active nurses assigned to this unit + any nurses with shifts in this roster period
            $unitNurses = Nurse::where('current_unit_id', $unit->id)
                ->where('status', '!=', 'inactive')
                ->get();
            $shiftNurses = $period->shifts->pluck('nurse')->filter();

            $nurses = $unitNurses->concat($shiftNurses)->unique('id')->sortBy([
                fn ($a, $b) => ($a->grade === 'In-Charge' ? 0 : 1) <=> ($b->grade === 'In-Charge' ? 0 : 1),
                fn ($a, $b) => strcmp($a->name, $b->name),
            ])->values();

            $byNurse = $period->shifts->groupBy('nurse_id')
                ->map(fn ($shifts) => $shifts->keyBy(fn ($s) => $s->shift_date->toDateString()));

            // Leave overlapping this period, grouped per nurse as [start, end, type].
            // Leave is never individually approved — it reflects automatically once
            // logged by the in-charge (only explicitly rejected leave is ignored).
            $nurseIds = $unitNurses->pluck('id')->concat($shiftNurses->pluck('id'))->unique();
            $leaveSegments = LeaveRequest::where('status', '!=', 'rejected')
                ->whereIn('nurse_id', $nurseIds)
                ->where('start_date', '<=', $period->period_end)
                ->where('end_date', '>=', $period->period_start)
                ->get()
                ->groupBy('nurse_id')
                ->map(fn ($leaves) => $leaves->map(fn ($leave) => [
                    'start' => $leave->start_date->max($period->period_start),
                    'end' => $leave->end_date->min($period->period_end),
                    'type' => $leave->type,
                ])->all())
                ->all();

            return [
                'period' => $period,
                'dates' => $dates,
                'nurses' => $nurses,
                'byNurse' => $byNurse,
                'leaveSegments' => $leaveSegments,
            ];
        });

        return view('public.roster', compact('units', 'unit', 'periodsData'));
    }

    /**
     * Search nurses by name for the public "find a nurse" autocomplete.
     * Returns active nurses whose name contains the query string.
     */
    public function searchNurses(Request $request): JsonResponse
    {
        $query = $request->input('q', '');

        $nurses = Nurse::where('name', 'like', '%'.$query.'%')
            ->where('status', '!=', 'inactive')
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'grade', 'current_unit_id'])
            ->load('currentUnit');

        return response()->json(
            $nurses->map(fn ($n) => [
                'id' => $n->id,
                'name' => $n->name,
                'grade' => $n->grade,
                'unit' => $n->currentUnit->name ?? '—',
                'url' => route('nurse.roster', $n),
            ])
        );
    }
}
