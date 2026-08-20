<?php

use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
});

test('admin can create a user account', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.users.store'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'role' => User::ROLE_HEAD_NURSING,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
});

test('in_charge role requires a unit_id', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.users.store'), [
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'password' => 'password123',
            'role' => User::ROLE_IN_CHARGE,
            'unit_id' => null,
        ])
        ->assertSessionHasErrors('unit_id');
});

test('admin role does not require a unit_id and unit is nulled', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.users.store'), [
            'name' => 'Admin Two',
            'email' => 'admin2@example.com',
            'password' => 'password123',
            'role' => User::ROLE_ADMIN,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('users', ['email' => 'admin2@example.com', 'unit_id' => null]);
});

test('admin can update a user', function () {
    $user = User::factory()->create(['role' => User::ROLE_HEAD_NURSING]);

    $this->actingAs($this->admin)
        ->put(route('admin.users.update', $user), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'role' => User::ROLE_HEAD_NURSING,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
});

test('admin cannot delete their own account', function () {
    $this->actingAs($this->admin)
        ->delete(route('admin.users.destroy', $this->admin))
        ->assertSessionHasErrors('user');
});

test('admin can delete another user', function () {
    $other = User::factory()->create(['role' => User::ROLE_HEAD_NURSING]);

    $this->actingAs($this->admin)
        ->delete(route('admin.users.destroy', $other))
        ->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseMissing('users', ['id' => $other->id]);
});

test('email must be unique on create', function () {
    $existing = User::factory()->create();

    $this->actingAs($this->admin)
        ->post(route('admin.users.store'), [
            'name' => 'Dup',
            'email' => $existing->email,
            'password' => 'password123',
            'role' => User::ROLE_ADMIN,
        ])
        ->assertSessionHasErrors('email');
});
