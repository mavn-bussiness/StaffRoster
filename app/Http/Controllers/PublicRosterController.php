<?php

namespace App\Http\Controllers;

use App\Models\RosterPeriod;
use App\Models\Unit;
use Carbon\CarbonPeriod;
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

        $periods = RosterPeriod::where('unit_id', $unit->id)
            ->whereIn('status', ['agreed', 'approved', 'submitted'])
            ->with(['shifts.nurse'])
            ->orderByDesc('period_start')
            ->take(3)
            ->get();

        $periodsData = $periods->map(function ($period) {
            return [
                'period' => $period,
                'dates' => CarbonPeriod::create($period->period_start, $period->period_end),
                'nurses' => $period->shifts->pluck('nurse')->unique('id')->sortBy('name')->values(),
                'byNurse' => $period->shifts->groupBy('nurse_id')
                    ->map(fn ($shifts) => $shifts->keyBy(fn ($s) => $s->shift_date->toDateString())),
            ];
        });

        return view('public.roster', compact('units', 'unit', 'periodsData'));
    }
}
