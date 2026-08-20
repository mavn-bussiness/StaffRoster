<?php

use App\Livewire\Admin\ManageUsers;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
});

test('ManageUsers renders for admin', function () {
    $this->actingAs($this->admin);

    Livewire::test(ManageUsers::class)->assertOk();
});

test('ManageUsers can create a head_nursing account', function () {
    $this->actingAs($this->admin);

    Livewire::test(ManageUsers::class)
        ->call('openCreate')
        ->set('name', 'Head Nurse')
        ->set('email', 'head@example.com')
        ->set('password', 'password123')
        ->set('role', User::ROLE_HEAD_NURSING)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', ['email' => 'head@example.com']);
});

test('ManageUsers requires unit for in_charge role', function () {
    $this->actingAs($this->admin);

    Livewire::test(ManageUsers::class)
        ->call('openCreate')
        ->set('name', 'In Charge')
        ->set('email', 'ic@example.com')
        ->set('password', 'password123')
        ->set('role', User::ROLE_IN_CHARGE)
        ->set('unit_id', null)
        ->call('save')
        ->assertHasErrors(['unit_id']);
});

test('ManageUsers can edit a user without changing password', function () {
    $user = User::factory()->create(['role' => User::ROLE_HEAD_NURSING]);
    $this->actingAs($this->admin);

    Livewire::test(ManageUsers::class)
        ->call('openEdit', $user)
        ->set('name', 'Updated')
        ->set('password', '')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated']);
});

test('ManageUsers cannot delete own account', function () {
    $this->actingAs($this->admin);

    Livewire::test(ManageUsers::class)
        ->call('delete', $this->admin);

    $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
});

test('ManageUsers can delete another user', function () {
    $other = User::factory()->create(['role' => User::ROLE_HEAD_NURSING]);
    $this->actingAs($this->admin);

    Livewire::test(ManageUsers::class)
        ->call('delete', $other);

    $this->assertDatabaseMissing('users', ['id' => $other->id]);
});
