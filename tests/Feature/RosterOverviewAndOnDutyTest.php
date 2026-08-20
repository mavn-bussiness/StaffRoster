<?php

use App\Models\Nurse;
use App\Models\RosterPeriod;
use App\Models\Shift;
use App\Models\Unit;
use App\Models\User;

// --- RosterOverviewController ---

test('admin can access rosters index', function () {
    $user = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($user)->get(route('rosters.index'))->assertOk();
});

test('head_nursing can access rosters index', function () {
    $user = User::factory()->create(['role' => User::ROLE_HEAD_NURSING]);

    $this->actingAs($user)->get(route('rosters.index'))->assertOk();
});

test('area_manager can access rosters index', function () {
    $unit = Unit::factory()->create();
    $user = User::factory()->create(['role' => User::ROLE_AREA_MANAGER, 'unit_id' => $unit->id]);

    $this->actingAs($user)->get(route('rosters.index'))->assertOk();
});

test('in_charge cannot access rosters index', function () {
    $unit = Unit::factory()->create();
    $user = User::factory()->create(['role' => User::ROLE_IN_CHARGE, 'unit_id' => $unit->id]);

    $this->actingAs($user)->get(route('rosters.index'))->assertForbidden();
});

test('rosters index can be filtered by unit_id', function () {
    $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $unit = Unit::factory()->create();

    $this->actingAs($user)
        ->get(route('rosters.index', ['unit_id' => $unit->id]))
        ->assertOk();
});

test('admin can print a roster period', function () {
    $unit = Unit::factory()->create();
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $period = RosterPeriod::factory()->create(['unit_id' => $unit->id, 'created_by' => $admin->id, 'status' => 'submitted']);

    $this->actingAs($admin)
        ->get(route('rosters.print', $period))
        ->assertOk();
});

// --- OnDutyController ---

test('admin can access on-duty index', function () {
    $user = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($user)->get(route('on-duty.index'))->assertOk();
});

test('in_charge cannot access on-duty index', function () {
    $unit = Unit::factory()->create();
    $user = User::factory()->create(['role' => User::ROLE_IN_CHARGE, 'unit_id' => $unit->id]);

    $this->actingAs($user)->get(route('on-duty.index'))->assertForbidden();
});

test('on-duty index shows nurses on duty today', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $unit = Unit::factory()->create();
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);
    $period = RosterPeriod::factory()->create(['unit_id' => $unit->id, 'created_by' => $admin->id, 'status' => 'submitted']);

    Shift::create([
        'roster_period_id' => $period->id,
        'nurse_id' => $nurse->id,
        'shift_date' => now()->toDateString(),
        'code' => 'D',
    ]);

    $this->actingAs($admin)
        ->get(route('on-duty.index'))
        ->assertOk()
        ->assertSee($nurse->name);
});

test('on-duty index excludes off-day shifts', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $unit = Unit::factory()->create();
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);
    $period = RosterPeriod::factory()->create(['unit_id' => $unit->id, 'created_by' => $admin->id, 'status' => 'submitted']);

    Shift::create([
        'roster_period_id' => $period->id,
        'nurse_id' => $nurse->id,
        'shift_date' => now()->toDateString(),
        'code' => 'O',
    ]);

    $this->actingAs($admin)
        ->get(route('on-duty.index'))
        ->assertOk()
        ->assertDontSee($nurse->name);
});
