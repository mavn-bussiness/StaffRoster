<?php

use App\Livewire\InCharge\ManageOffDayRequests;
use App\Models\Nurse;
use App\Models\OffDayRequest;
use App\Models\RosterPeriod;
use App\Models\Unit;
use App\Models\User;
use Livewire\Livewire;

// --- ManageOffDayRequests ---

test('ManageOffDayRequests renders for in_charge', function () {
    $unit = Unit::factory()->create();
    $user = User::factory()->create(['role' => User::ROLE_IN_CHARGE, 'unit_id' => $unit->id]);
    $this->actingAs($user);

    Livewire::test(ManageOffDayRequests::class)->assertOk();
});

test('ManageOffDayRequests can create an off-day request', function () {
    $unit = Unit::factory()->create();
    $user = User::factory()->create(['role' => User::ROLE_IN_CHARGE, 'unit_id' => $unit->id]);
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);
    $period = RosterPeriod::factory()->create(['unit_id' => $unit->id, 'created_by' => $user->id, 'status' => 'draft']);
    $this->actingAs($user);

    Livewire::test(ManageOffDayRequests::class)
        ->call('openCreate')
        ->set('nurse_id', $nurse->id)
        ->set('roster_period_id', $period->id)
        ->set('requested_date', '2025-08-15')
        ->set('status', 'requested')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('off_day_requests', ['nurse_id' => $nurse->id, 'status' => 'requested']);
});

test('ManageOffDayRequests can delete an off-day request', function () {
    $unit = Unit::factory()->create();
    $user = User::factory()->create(['role' => User::ROLE_IN_CHARGE, 'unit_id' => $unit->id]);
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);
    $period = RosterPeriod::factory()->create(['unit_id' => $unit->id, 'created_by' => $user->id]);
    $request = OffDayRequest::factory()->create(['nurse_id' => $nurse->id, 'roster_period_id' => $period->id]);
    $this->actingAs($user);

    Livewire::test(ManageOffDayRequests::class)
        ->call('delete', $request);

    $this->assertDatabaseMissing('off_day_requests', ['id' => $request->id]);
});
