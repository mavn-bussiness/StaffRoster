<?php

use App\Models\Nurse;
use App\Models\Unit;
use App\Models\User;

function adminUser(): User
{
    return User::factory()->create(['role' => User::ROLE_ADMIN]);
}

test('non-admin cannot access unit management', function () {
    $user = User::factory()->create(['role' => User::ROLE_IN_CHARGE, 'unit_id' => Unit::factory()->create()->id]);
    $this->actingAs($user)->get(route('admin.units.index'))->assertForbidden();
});

test('admin can create a unit', function () {
    $this->actingAs(adminUser())
        ->post(route('admin.units.store'), [
            'name' => 'Cardiology',
            'code' => 'CARD',
            'has_evening_shift' => '1',
            'has_night_shift' => '0',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('units', ['code' => 'CARD']);
});

test('unit code must be unique on create', function () {
    Unit::factory()->create(['code' => 'DUP1']);

    $this->actingAs(adminUser())
        ->post(route('admin.units.store'), ['name' => 'Other', 'code' => 'DUP1'])
        ->assertSessionHasErrors('code');
});

test('admin can update a unit', function () {
    $unit = Unit::factory()->create(['name' => 'Old Name']);

    $this->actingAs(adminUser())
        ->put(route('admin.units.update', $unit), [
            'name' => 'New Name',
            'code' => $unit->code,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('units', ['id' => $unit->id, 'name' => 'New Name']);
});

test('admin can delete a unit with no nurses or users', function () {
    $unit = Unit::factory()->create();

    $this->actingAs(adminUser())
        ->delete(route('admin.units.destroy', $unit))
        ->assertRedirect(route('admin.units.index'));

    $this->assertDatabaseMissing('units', ['id' => $unit->id]);
});

test('cannot delete a unit that has nurses', function () {
    $unit = Unit::factory()->create();
    Nurse::factory()->create(['home_unit_id' => $unit->id, 'current_unit_id' => $unit->id]);

    $this->actingAs(adminUser())
        ->delete(route('admin.units.destroy', $unit))
        ->assertSessionHasErrors('unit');

    $this->assertDatabaseHas('units', ['id' => $unit->id]);
});
