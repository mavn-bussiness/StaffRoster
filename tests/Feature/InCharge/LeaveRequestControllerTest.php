<?php

use App\Filament\Resources\LeaveRequests\Pages\CreateLeaveRequest;
use App\Filament\Resources\LeaveRequests\Pages\ListLeaveRequests;
use App\Models\LeaveRequest;
use App\Models\Nurse;
use App\Models\Unit;
use App\Models\User;
use Livewire\Livewire;

function leaveInCharge(): array
{
    $unit = Unit::factory()->create();
    $user = User::factory()->create(['role' => User::ROLE_IN_CHARGE, 'unit_id' => $unit->id]);
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);

    return [$user, $unit, $nurse];
}

test('in_charge can see only leave requests for their unit', function () {
    [$user, $unit, $nurse] = leaveInCharge();
    $mine = LeaveRequest::factory()->create(['nurse_id' => $nurse->id]);
    $other = LeaveRequest::factory()->create(); // nurse from different unit

    $this->actingAs($user);

    Livewire::test(ListLeaveRequests::class)
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$other]);
});

test('in_charge can log a leave request for a nurse in their unit', function () {
    [$user, , $nurse] = leaveInCharge();

    $this->actingAs($user);

    Livewire::test(CreateLeaveRequest::class)
        ->fillForm([
            'nurse_id' => $nurse->id,
            'type' => 'annual',
            'start_date' => '2025-09-01',
            'end_date' => '2025-09-07',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('leave_requests', [
        'nurse_id' => $nurse->id,
        'type' => 'annual',
        'status' => 'pending',
    ]);
});

test('leave request requires nurse, type, start and end date', function () {
    [$user] = leaveInCharge();

    $this->actingAs($user);

    Livewire::test(CreateLeaveRequest::class)
        ->fillForm([
            'nurse_id' => null,
            'type' => null,
            'start_date' => null,
            'end_date' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['nurse_id', 'type', 'start_date', 'end_date']);
});

test('end_date must be on or after start_date', function () {
    [$user, , $nurse] = leaveInCharge();

    $this->actingAs($user);

    Livewire::test(CreateLeaveRequest::class)
        ->fillForm([
            'nurse_id' => $nurse->id,
            'type' => 'sick',
            'start_date' => '2025-09-10',
            'end_date' => '2025-09-05',
        ])
        ->call('create')
        ->assertHasFormErrors(['end_date']);
});

test('nurse from another unit does not appear in leave request form', function () {
    [$user] = leaveInCharge();
    $otherNurse = Nurse::factory()->create(); // different unit

    $this->actingAs($user);

    $options = Nurse::where('current_unit_id', $user->unit_id)->pluck('id')->toArray();
    expect($options)->not->toContain($otherNurse->id);
});
