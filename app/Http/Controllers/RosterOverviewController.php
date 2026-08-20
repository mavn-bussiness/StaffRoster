<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Nurse;
use App\Models\RosterPeriod;
use App\Models\Unit;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Read-only cross-unit view for admin, area manager, and head nursing.
 * All three roles see every unit (User::seesAllUnits()).
 */
class RosterOverviewController extends Controller
{
    public function index(Request $request): View
    {
        $units = Unit::orderBy('name')->get();

        $periods = RosterPeriod::with('unit')
            ->when($request->filled('unit_id'), fn ($q) => $q->where('unit_id', $request->integer('unit_id')))
            ->orderByDesc('period_start')
            ->get();

        return view('rosters.index', compact('units', 'periods'));
    }

    /**
     * A read-only, print-friendly grid — this is what head nursing prints and
     * physically distributes to administrators and MoH.
     */
    public function print(RosterPeriod $rosterPeriod): View
    {
        $rosterPeriod->load(['unit.users', 'shifts.nurse', 'approvedBy', 'reviewedBy', 'createdBy']);

        // Ensure in-charge is registered as a nurse for this unit
        $rosterPeriod->unit?->syncInChargeNurse();

        $dates = CarbonPeriod::create($rosterPeriod->period_start, $rosterPeriod->period_end);

        // Get all active nurses assigned to this unit + any nurses with shifts in this roster period
        $unitNurses = Nurse::where('current_unit_id', $rosterPeriod->unit_id)
            ->where('status', '!=', 'inactive')
            ->get();
        $shiftNurses = $rosterPeriod->shifts->pluck('nurse')->filter();

        $nurses = $unitNurses->concat($shiftNurses)->unique('id')->sortBy([
            fn ($a, $b) => ($a->grade === 'In-Charge' ? 0 : 1) <=> ($b->grade === 'In-Charge' ? 0 : 1),
            fn ($a, $b) => strcmp($a->name, $b->name),
        ]);

        $shiftsByNurseAndDate = $rosterPeriod->shifts
            ->groupBy('nurse_id')
            ->map(fn ($shifts) => $shifts->keyBy(fn ($s) => $s->shift_date->toDateString()));

        // Leave overlapping this period, grouped per nurse as [start, end, type].
        // Leave is never individually approved — it reflects automatically once
        // logged by the in-charge (only explicitly rejected leave is ignored).
        $leaveNurseIds = $unitNurses->pluck('id')->concat($shiftNurses->pluck('id'))->unique();
        $leaveSegments = LeaveRequest::where('status', '!=', 'rejected')
            ->whereIn('nurse_id', $leaveNurseIds)
            ->where('start_date', '<=', $rosterPeriod->period_end)
            ->where('end_date', '>=', $rosterPeriod->period_start)
            ->get()
            ->groupBy('nurse_id')
            ->map(fn ($leaves) => $leaves->map(fn ($leave) => [
                'start' => $leave->start_date->max($rosterPeriod->period_start),
                'end' => $leave->end_date->min($rosterPeriod->period_end),
                'type' => $leave->type,
            ])->all())
            ->all();

        $inCharge = User::where('unit_id', $rosterPeriod->unit_id)
            ->where('role', User::ROLE_IN_CHARGE)
            ->first() ?? $rosterPeriod->createdBy;

        return view('rosters.print', [
            'rosterPeriod' => $rosterPeriod,
            'dates' => $dates,
            'nurses' => $nurses,
            'shiftsByNurseAndDate' => $shiftsByNurseAndDate,
            'leaveSegments' => $leaveSegments,
            'leaveTypeLabels' => [
                'annual' => 'Annual',
                'sick' => 'Sick',
                'maternity' => 'Maternity',
                'compassionate' => 'Compassionate',
                'other' => 'Other',
            ],
            'inCharge' => $inCharge,
        ]);
    }
}
