<?php

use App\Livewire\InCharge\ManageNurses;
use App\Models\Nurse;
use App\Models\Unit;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->unit = Unit::factory()->create();
    $this->user = User::factory()->create(['role' => User::ROLE_IN_CHARGE, 'unit_id' => $this->unit->id]);
});

test('ManageNurses renders for in_charge', function () {
    $this->actingAs($this->user);

    Livewire::test(ManageNurses::class)->assertOk();
});

test('ManageNurses can add a nurse', function () {
    $this->actingAs($this->user);

    Livewire::test(ManageNurses::class)
        ->call('openCreate')
        ->set('name', 'New Nurse')
        ->set('status', 'active')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('nurses', ['name' => 'New Nurse', 'current_unit_id' => $this->unit->id]);
});

test('ManageNurses can edit a nurse', function () {
    $nurse = Nurse::factory()->create(['current_unit_id' => $this->unit->id, 'home_unit_id' => $this->unit->id]);
    $this->actingAs($this->user);

    Livewire::test(ManageNurses::class)
        ->call('openEdit', $nurse)
        ->set('name', 'Edited Name')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('nurses', ['id' => $nurse->id, 'name' => 'Edited Name']);
});

test('ManageNurses can delete a nurse', function () {
    $nurse = Nurse::factory()->create(['current_unit_id' => $this->unit->id, 'home_unit_id' => $this->unit->id]);
    $this->actingAs($this->user);

    Livewire::test(ManageNurses::class)
        ->call('delete', $nurse);

    $this->assertDatabaseMissing('nurses', ['id' => $nurse->id]);
});

test('ManageNurses search filters results', function () {
    Nurse::factory()->create(['name' => 'Alice Smith', 'current_unit_id' => $this->unit->id, 'home_unit_id' => $this->unit->id]);
    Nurse::factory()->create(['name' => 'Bob Jones', 'current_unit_id' => $this->unit->id, 'home_unit_id' => $this->unit->id]);
    $this->actingAs($this->user);

    Livewire::test(ManageNurses::class)
        ->set('search', 'Alice')
        ->assertSee('Alice Smith')
        ->assertDontSee('Bob Jones');
});
