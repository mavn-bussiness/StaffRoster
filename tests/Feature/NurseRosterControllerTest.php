<?php

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

test('nurse roster shows only agreed/notified/submitted periods', function () {
    $unit = Unit::factory()->create();
    $user = User::factory()->create(['role' => User::ROLE_IN_CHARGE, 'unit_id' => $unit->id]);
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);

    $published = RosterPeriod::factory()->create(['unit_id' => $unit->id, 'created_by' => $user->id, 'status' => 'agreed']);
    $draft = RosterPeriod::factory()->create(['unit_id' => $unit->id, 'created_by' => $user->id, 'status' => 'draft']);

    Shift::create(['roster_period_id' => $published->id, 'nurse_id' => $nurse->id, 'shift_date' => $published->period_start, 'code' => 'D']);
    Shift::create(['roster_period_id' => $draft->id, 'nurse_id' => $nurse->id, 'shift_date' => $draft->period_start, 'code' => 'D']);

    $response = $this->get(route('nurse.roster', $nurse));

    $response->assertOk();
    $response->assertViewHas('periods', fn ($periods) => $periods->contains('id', $published->id) && ! $periods->contains('id', $draft->id)
    );
});

test('nurse roster shows the nurse name', function () {
    $unit = Unit::factory()->create();
    $nurse = Nurse::factory()->create(['name' => 'Nurse Testable', 'current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);

    $this->get(route('nurse.roster', $nurse))->assertSee('Nurse Testable');
});
