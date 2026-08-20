<?php

use App\Models\Unit;
use App\Models\User;

test('login page is accessible to guests', function () {
    $this->get('/login')->assertOk();
});

test('authenticated user is redirected away from login', function () {
    $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $this->actingAs($user)->get('/login')->assertRedirect();
});

test('valid credentials log the user in', function () {
    $user = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'password' => bcrypt('secret123'),
    ]);

    $this->post('/login', ['email' => $user->email, 'password' => 'secret123'])
        ->assertRedirect(route('admin.units.index'));

    $this->assertAuthenticatedAs($user);
});

test('invalid credentials return validation error', function () {
    $user = User::factory()->create(['password' => bcrypt('correct')]);

    $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('admin is redirected to units index after login', function () {
    $user = User::factory()->create(['role' => User::ROLE_ADMIN, 'password' => bcrypt('pass')]);
    $this->post('/login', ['email' => $user->email, 'password' => 'pass'])
        ->assertRedirect(route('admin.units.index'));
});

test('in_charge is redirected to roster periods after login', function () {
    $unit = Unit::factory()->create();
    $user = User::factory()->create(['role' => User::ROLE_IN_CHARGE, 'unit_id' => $unit->id, 'password' => bcrypt('pass')]);
    $this->post('/login', ['email' => $user->email, 'password' => 'pass'])
        ->assertRedirect(route('incharge.roster-periods.index'));
});

test('area_manager is redirected to rosters index after login', function () {
    $unit = Unit::factory()->create();
    $user = User::factory()->create(['role' => User::ROLE_AREA_MANAGER, 'unit_id' => $unit->id, 'password' => bcrypt('pass')]);
    $this->post('/login', ['email' => $user->email, 'password' => 'pass'])
        ->assertRedirect(route('rosters.index'));
});

test('logout clears session and redirects to login', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->post('/logout')
        ->assertRedirect(route('login'));
    $this->assertGuest();
});
