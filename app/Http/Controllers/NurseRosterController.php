<?php

namespace App\Http\Controllers;

use App\Models\Nurse;
use App\Models\RosterPeriod;
use Illuminate\View\View;

class NurseRosterController extends Controller
{
    public function show(Nurse $nurse): View
    {
        $periods = RosterPeriod::where('unit_id', $nurse->current_unit_id)
            ->whereIn('status', ['agreed', 'notified', 'submitted'])
            ->with(['shifts' => fn ($q) => $q->where('nurse_id', $nurse->id)])
            ->orderByDesc('period_start')
            ->take(3)
            ->get();

        return view('nurse.roster', compact('nurse', 'periods'));
    }
}
