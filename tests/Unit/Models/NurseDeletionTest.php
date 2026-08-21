<?php

use App\Models\LeaveRequest;
use App\Models\Nurse;
use App\Models\OffDayRequest;
use App\Models\RosterPeriod;
use App\Models\Shift;
use App\Models\Transfer;
use App\Models\Unit;

test('deleting a nurse cascades to shifts', function () {
    $unit = Unit::factory()->create();
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);
    $rosterPeriod = RosterPeriod::factory()->create(['unit_id' => $unit->id]);

    Shift::create([
        'roster_period_id' => $rosterPeriod->id,
        'nurse_id' => $nurse->id,
        'shift_date' => now()->toDateString(),
        'code' => 'D',
    ]);

    $nurse->delete();

    expect(Shift::where('nurse_id', $nurse->id)->exists())->toBeFalse();
});

test('deleting a nurse cascades to off day requests', function () {
    $unit = Unit::factory()->create();
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);
    $rosterPeriod = RosterPeriod::factory()->create(['unit_id' => $unit->id]);

    OffDayRequest::factory()->create(['nurse_id' => $nurse->id, 'roster_period_id' => $rosterPeriod->id]);

    $nurse->delete();

    expect(OffDayRequest::where('nurse_id', $nurse->id)->exists())->toBeFalse();
});

test('deleting a nurse cascades to leave requests', function () {
    $nurse = Nurse::factory()->create();

    LeaveRequest::factory()->create(['nurse_id' => $nurse->id]);

    $nurse->delete();

    expect(LeaveRequest::where('nurse_id', $nurse->id)->exists())->toBeFalse();
});

test('deleting a nurse cascades to transfers', function () {
    $unit = Unit::factory()->create();
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);

    Transfer::factory()->create([
        'nurse_id' => $nurse->id,
        'from_unit_id' => $unit->id,
        'to_unit_id' => $unit->id,
        'start_date' => now()->toDateString(),
        'status' => 'pending',
    ]);

    $nurse->delete();

    expect(Transfer::where('nurse_id', $nurse->id)->exists())->toBeFalse();
});
