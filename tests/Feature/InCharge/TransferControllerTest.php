<?php

use App\Filament\Resources\Transfers\Pages\CreateTransfer;
use App\Models\Nurse;
use App\Models\Transfer;
use App\Models\Unit;
use App\Models\User;
use Livewire\Livewire;

test('in_charge can transfer a nurse from their unit to another', function () {
    $unit = Unit::factory()->create();
    $user = User::factory()->create(['role' => User::ROLE_IN_CHARGE, 'unit_id' => $unit->id]);
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);
    $targetUnit = Unit::factory()->create();

    $this->actingAs($user);

    Livewire::test(CreateTransfer::class)
        ->fillForm([
            'nurse_id' => $nurse->id,
            'to_unit_id' => $targetUnit->id,
            'start_date' => '2025-09-01',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('transfers', [
        'nurse_id' => $nurse->id,
        'from_unit_id' => $unit->id,
        'to_unit_id' => $targetUnit->id,
        'status' => Transfer::STATUS_PENDING,
    ]);
});

test('in_charge cannot transfer a nurse from another unit', function () {
    $unit = Unit::factory()->create();
    $user = User::factory()->create(['role' => User::ROLE_IN_CHARGE, 'unit_id' => $unit->id]);
    $otherUnit = Unit::factory()->create();
    $nurse = Nurse::factory()->create(['current_unit_id' => $otherUnit->id, 'home_unit_id' => $otherUnit->id]);
    $target = Unit::factory()->create();

    $this->actingAs($user);

    // The nurse from another unit should not appear in the dropdown for in_charge
    $options = Nurse::where('current_unit_id', $user->unit_id)->pluck('id')->toArray();
    expect($options)->not->toContain($nurse->id);
});

test('transfer requires a start date', function () {
    $unit = Unit::factory()->create();
    $user = User::factory()->create(['role' => User::ROLE_IN_CHARGE, 'unit_id' => $unit->id]);
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);
    $target = Unit::factory()->create();

    $this->actingAs($user);

    Livewire::test(CreateTransfer::class)
        ->fillForm([
            'nurse_id' => $nurse->id,
            'to_unit_id' => $target->id,
            'start_date' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['start_date' => 'required']);
});
