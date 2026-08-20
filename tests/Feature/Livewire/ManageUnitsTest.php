<?php

use App\Livewire\Admin\ManageUnits;
use App\Models\Nurse;
use App\Models\Unit;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
});

test('ManageUnits renders for admin', function () {
    $this->actingAs($this->admin);

    Livewire::test(ManageUnits::class)->assertOk();
});

test('ManageUnits can create a unit', function () {
    $this->actingAs($this->admin);

    Livewire::test(ManageUnits::class)
        ->call('openCreate')
        ->set('name', 'Oncology')
        ->set('code', 'ONC')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('units', ['code' => 'ONC']);
});

test('ManageUnits validates unique code on create', function () {
    Unit::factory()->create(['code' => 'DUP']);
    $this->actingAs($this->admin);

    Livewire::test(ManageUnits::class)
        ->call('openCreate')
        ->set('name', 'Duplicate')
        ->set('code', 'DUP')
        ->call('save')
        ->assertHasErrors(['code']);
});

test('ManageUnits can edit an existing unit', function () {
    $unit = Unit::factory()->create(['name' => 'Old']);
    $this->actingAs($this->admin);

    Livewire::test(ManageUnits::class)
        ->call('openEdit', $unit)
        ->set('name', 'New Name')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('units', ['id' => $unit->id, 'name' => 'New Name']);
});

test('ManageUnits can delete a unit with no nurses', function () {
    $unit = Unit::factory()->create();
    $this->actingAs($this->admin);

    Livewire::test(ManageUnits::class)
        ->call('delete', $unit);

    $this->assertDatabaseMissing('units', ['id' => $unit->id]);
});

test('ManageUnits cannot delete a unit that has nurses', function () {
    $unit = Unit::factory()->create();
    Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);
    $this->actingAs($this->admin);

    Livewire::test(ManageUnits::class)
        ->call('delete', $unit);

    $this->assertDatabaseHas('units', ['id' => $unit->id]);
});
