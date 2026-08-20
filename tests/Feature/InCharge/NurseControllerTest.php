<?php

use App\Filament\Resources\Nurses\NurseResource;
use App\Filament\Resources\Nurses\Pages\CreateNurse;
use App\Filament\Resources\Nurses\Pages\EditNurse;
use App\Filament\Resources\Nurses\Pages\ListNurses;
use App\Models\Nurse;
use App\Models\Unit;
use App\Models\User;
use Livewire\Livewire;

function inChargeWithUnit(): array
{
    $unit = Unit::factory()->create();
    $user = User::factory()->create(['role' => User::ROLE_IN_CHARGE, 'unit_id' => $unit->id]);

    return [$user, $unit];
}

test('in_charge can view nurses list for their unit', function () {
    [$user, $unit] = inChargeWithUnit();
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);
    $other = Nurse::factory()->create(); // different unit

    $this->actingAs($user);

    Livewire::test(ListNurses::class)
        ->assertCanSeeTableRecords([$nurse])
        ->assertCanNotSeeTableRecords([$other]);
});

test('in_charge can create a nurse assigned to their unit', function () {
    [$user] = inChargeWithUnit();

    $this->actingAs($user);

    Livewire::test(CreateNurse::class)
        ->fillForm([
            'name' => 'Nurse Alice',
            'grade' => 'Staff Nurse',
            'employment_type' => 'Permanent',
            'status' => 'active',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('nurses', [
        'name' => 'Nurse Alice',
        'current_unit_id' => $user->unit_id,
        'home_unit_id' => $user->unit_id,
    ]);
});

test('in_charge cannot create a nurse without a name', function () {
    [$user] = inChargeWithUnit();

    $this->actingAs($user);

    Livewire::test(CreateNurse::class)
        ->fillForm(['name' => ''])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

test('in_charge can edit a nurse in their unit', function () {
    [$user, $unit] = inChargeWithUnit();
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);

    $this->actingAs($user);

    Livewire::test(EditNurse::class, ['record' => $nurse->getRouteKey()])
        ->fillForm([
            'name' => 'Updated Name',
            'status' => 'active',
            'employment_type' => 'Permanent',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($nurse->fresh()->name)->toBe('Updated Name');
});

test('in_charge cannot delete a nurse', function () {
    [$user, $unit] = inChargeWithUnit();
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);

    $this->actingAs($user);

    expect(NurseResource::canDelete($nurse))->toBeFalse();
});

test('admin can delete a nurse', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $nurse = Nurse::factory()->create();

    $this->actingAs($admin);

    expect(NurseResource::canDelete($nurse))->toBeTrue();
});
