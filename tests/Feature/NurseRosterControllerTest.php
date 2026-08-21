<?php

use App\Models\LeaveRequest;
use App\Models\Nurse;
use App\Models\RosterPeriod;
use App\Models\Shift;
use App\Models\Unit;
use App\Models\User;

test('nurse roster page is publicly accessible without login', function () {
    $unit = Unit::factory()->create();
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);

    $this->get(route('nurse.roster', $nurse))->assertOk();
});

test('nurse roster shows only agreed/approved/submitted periods', function () {
    $unit = Unit::factory()->create();
    $user = User::factory()->create(['role' => User::ROLE_IN_CHARGE, 'unit_id' => $unit->id]);
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);

    $published = RosterPeriod::factory()->create(['unit_id' => $unit->id, 'created_by' => $user->id, 'status' => 'agreed']);
    $draft = RosterPeriod::factory()->create(['unit_id' => $unit->id, 'created_by' => $user->id, 'status' => 'draft']);

    Shift::create(['roster_period_id' => $published->id, 'nurse_id' => $nurse->id, 'shift_date' => $published->period_start, 'code' => 'D']);
    Shift::create(['roster_period_id' => $draft->id, 'nurse_id' => $nurse->id, 'shift_date' => $draft->period_start, 'code' => 'D']);

    $response = $this->get(route('nurse.roster', $nurse));

    $response->assertOk();
    $response->assertViewHas('periodsData', fn ($periodsData) => $periodsData->contains(fn ($item) => $item['period']->id === $published->id) &&
        ! $periodsData->contains(fn ($item) => $item['period']->id === $draft->id)
    );
});

test('nurse roster shows the nurse name', function () {
    $unit = Unit::factory()->create();
    $nurse = Nurse::factory()->create(['name' => 'Nurse Testable', 'current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);

    $this->get(route('nurse.roster', $nurse))->assertSee('Nurse Testable');
});

test('nurse roster shows leave segments', function () {
    $unit = Unit::factory()->create();
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);

    $period = RosterPeriod::factory()->create([
        'unit_id' => $unit->id,
        'status' => 'submitted',
        'period_start' => '2025-01-01',
        'period_end' => '2025-01-31',
    ]);

    LeaveRequest::create([
        'nurse_id' => $nurse->id,
        'type' => 'annual',
        'start_date' => '2025-01-10',
        'end_date' => '2025-01-12',
        'status' => 'pending',
    ]);

    $response = $this->get(route('nurse.roster', $nurse));

    $response->assertViewHas('periodsData', function ($periodsData) use ($period) {
        $item = $periodsData->first(fn ($i) => $i['period']->id === $period->id);

        return $item['leaveSegments'] !== null && count($item['leaveSegments']) === 1;
    });
    $response->assertOk();
    $response->assertSee('ANNUAL LEAVE');
});

test('nurse roster excludes rejected leave', function () {
    $unit = Unit::factory()->create();
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);

    $period = RosterPeriod::factory()->create([
        'unit_id' => $unit->id,
        'status' => 'submitted',
        'period_start' => '2025-01-01',
        'period_end' => '2025-01-31',
    ]);

    LeaveRequest::create([
        'nurse_id' => $nurse->id,
        'type' => 'sick',
        'start_date' => '2025-01-10',
        'end_date' => '2025-01-12',
        'status' => 'rejected',
    ]);

    $response = $this->get(route('nurse.roster', $nurse));

    $response->assertViewHas('periodsData', function ($periodsData) use ($period) {
        $item = $periodsData->first(fn ($i) => $i['period']->id === $period->id);

        return count($item['leaveSegments']) === 0;
    });
    $response->assertDontSee('SICK LEAVE');
});
