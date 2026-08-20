<?php

use App\Models\User;

test('isAdmin returns true only for admin role', function () {
    $admin = User::factory()->make(['role' => User::ROLE_ADMIN]);
    $other = User::factory()->make(['role' => User::ROLE_IN_CHARGE]);

    expect($admin->isAdmin())->toBeTrue();
    expect($other->isAdmin())->toBeFalse();
});

test('isInCharge returns true only for in_charge role', function () {
    $inCharge = User::factory()->make(['role' => User::ROLE_IN_CHARGE]);
    $other = User::factory()->make(['role' => User::ROLE_ADMIN]);

    expect($inCharge->isInCharge())->toBeTrue();
    expect($other->isInCharge())->toBeFalse();
});

test('seesAllUnits returns true for admin, head_nursing, area_manager', function () {
    foreach ([User::ROLE_ADMIN, User::ROLE_HEAD_NURSING, User::ROLE_AREA_MANAGER] as $role) {
        $user = User::factory()->make(['role' => $role]);
        expect($user->seesAllUnits())->toBeTrue("Expected {$role} to see all units");
    }
});

test('seesAllUnits returns false for in_charge', function () {
    $user = User::factory()->make(['role' => User::ROLE_IN_CHARGE]);

    expect($user->seesAllUnits())->toBeFalse();
});
