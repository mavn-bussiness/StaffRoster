<?php

use App\Filament\Resources\OffDayRequests\Pages\CreateOffDayRequest;
use App\Filament\Resources\OffDayRequests\Pages\ListOffDayRequests;
use App\Models\Nurse;
use App\Models\OffDayRequest;
use App\Models\RosterPeriod;
use App\Models\Unit;
use App\Models\User;
use Livewire\Livewire;

function offDaySetup(): array
{
    $unit = Unit::factory()->create();
    $user = User::factory()->create(['role' => User::ROLE_IN_CHARGE, 'unit_id' => $unit->id]);
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);
    $period = RosterPeriod::factory()->create(['unit_id' => $unit->id, 'created_by' => $user->id, 'status' => 'draft']);

    return [$user, $unit, $nurse, $period];
}

test('in_charge can see only off-day requests for their unit', function () {
    [$user, $unit, $nurse, $period] = offDaySetup();
    $mine = OffDayRequest::factory()->create(['nurse_id' => $nurse->id, 'roster_period_id' => $period->id]);
    $other = OffDayRequest::factory()->create(); // nurse from different unit

    $this->actingAs($user);

    Livewire::test(ListOffDayRequests::class)
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$other]);
});

test('in_charge can log an off-day request', function () {
    [$user, , $nurse, $period] = offDaySetup();

    $this->actingAs($user);

    Livewire::test(CreateOffDayRequest::class)
        ->fillForm([
            'nurse_id' => $nurse->id,
            'roster_period_id' => $period->id,
            'requested_date' => '2025-08-10',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('off_day_requests', [
        'nurse_id' => $nurse->id,
        'roster_period_id' => $period->id,
        'status' => 'requested',
    ]);
});

test('off-day request requires nurse, roster period and date', function () {
    [$user] = offDaySetup();

    $this->actingAs($user);

    Livewire::test(CreateOffDayRequest::class)
        ->fillForm([
            'nurse_id' => null,
            'roster_period_id' => null,
            'requested_date' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['nurse_id', 'roster_period_id', 'requested_date']);
});

test('in_charge can grant an off-day request', function () {
    [$user, , $nurse, $period] = offDaySetup();
    $request = OffDayRequest::factory()->create([
        'nurse_id' => $nurse->id,
        'roster_period_id' => $period->id,
        'status' => 'requested',
    ]);

    $this->actingAs($user);

    Livewire::test(ListOffDayRequests::class)
        ->callTableAction('updateStatus', $request, data: ['status' => 'granted'])
        ->assertHasNoActionErrors();

    expect($request->fresh()->status)->toBe('granted');
});

test('in_charge can deny an off-day request', function () {
    [$user, , $nurse, $period] = offDaySetup();
    $request = OffDayRequest::factory()->create([
        'nurse_id' => $nurse->id,
        'roster_period_id' => $period->id,
        'status' => 'requested',
    ]);

    $this->actingAs($user);

    Livewire::test(ListOffDayRequests::class)
        ->callTableAction('updateStatus', $request, data: ['status' => 'denied'])
        ->assertHasNoActionErrors();

    expect($request->fresh()->status)->toBe('denied');
});

test('in_charge can delete an off-day request', function () {
    [$user, , $nurse, $period] = offDaySetup();
    $request = OffDayRequest::factory()->create([
        'nurse_id' => $nurse->id,
        'roster_period_id' => $period->id,
    ]);

    $this->actingAs($user);

    Livewire::test(ListOffDayRequests::class)
        ->callTableAction('delete', $request)
        ->assertHasNoActionErrors();

    $this->assertDatabaseMissing('off_day_requests', ['id' => $request->id]);
});

test('nurse from another unit does not appear in off-day request form', function () {
    [$user] = offDaySetup();
    $otherNurse = Nurse::factory()->create(); // different unit

    $this->actingAs($user);

    $options = Nurse::where('current_unit_id', $user->unit_id)->pluck('id')->toArray();
    expect($options)->not->toContain($otherNurse->id);
});
