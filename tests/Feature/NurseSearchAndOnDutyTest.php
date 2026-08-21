<?php

use App\Filament\Pages\OnDuty;
use App\Models\LeaveRequest;
use App\Models\Nurse;
use App\Models\RosterPeriod;
use App\Models\Shift;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Carbon;

/*
 * --- Nurse Search (public) ---
 */

test('nurse search endpoint returns matching nurses', function () {
    $unit = Unit::factory()->create(['name' => 'ICU']);
    $nurse1 = Nurse::factory()->create(['name' => 'Alice Nalwanga', 'current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);
    $nurse2 = Nurse::factory()->create(['name' => 'Bob Ssali', 'current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);

    $response = $this->get(route('public.nurses.search', ['q' => 'Alice']));

    $response->assertOk()
        ->assertJsonStructure([['id', 'name', 'grade', 'unit', 'url']])
        ->assertJsonPath('0.name', 'Alice Nalwanga')
        ->assertJsonMissingPath('1.name');
});

test('nurse search endpoint is case insensitive', function () {
    $unit = Unit::factory()->create();
    $nurse = Nurse::factory()->create(['name' => 'Grace Wanjiru', 'current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);

    $response = $this->get(route('public.nurses.search', ['q' => 'grace']));

    $response->assertOk()
        ->assertJsonPath('0.name', 'Grace Wanjiru');
});

test('nurse search endpoint excludes inactive nurses', function () {
    $unit = Unit::factory()->create();
    $active = Nurse::factory()->create(['name' => 'Active Nurse', 'current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);
    $inactive = Nurse::factory()->create(['name' => 'Inactive Nurse', 'current_unit_id' => $unit->id, 'home_unit_id' => $unit->id, 'status' => 'inactive']);

    $response = $this->get(route('public.nurses.search', ['q' => 'Nurse']));

    $response->assertOk()
        ->assertJsonMissingPath('1.name');
});

test('public roster page has nurse search input', function () {
    $this->get(route('public.roster'))->assertOk()
        ->assertSee('nurse-search')
        ->assertSee('search by nurse name');
});

test('public roster page includes nurse search endpoint script', function () {
    $this->get(route('public.roster'))->assertOk()
        ->assertSee(route('public.nurses.search'));
});

test('public roster page shows leave legend when periods exist', function () {
    $unit = Unit::factory()->create();
    $user = User::factory()->create(['role' => User::ROLE_IN_CHARGE, 'unit_id' => $unit->id]);
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);

    $period = RosterPeriod::factory()->create([
        'unit_id' => $unit->id,
        'created_by' => $user->id,
        'status' => 'submitted',
    ]);

    Shift::create([
        'roster_period_id' => $period->id,
        'nurse_id' => $nurse->id,
        'shift_date' => $period->period_start,
        'code' => 'D',
    ]);

    $response = $this->get(route('public.roster.unit', $unit));

    $response->assertOk()
        ->assertSee('On Leave')
        ->assertSee('Annual')
        ->assertSee('Sick')
        ->assertSee('Maternity')
        ->assertSee('Compassionate')
        ->assertSee('Other');
});

test('public roster page shows leave segments with type labels', function () {
    $unit = Unit::factory()->create();
    $nurse = Nurse::factory()->create(['name' => 'Leave Nurse', 'current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);

    $period = RosterPeriod::factory()->create([
        'unit_id' => $unit->id,
        'status' => 'submitted',
        'period_start' => '2025-01-10',
        'period_end' => '2025-01-20',
    ]);

    Shift::create([
        'roster_period_id' => $period->id,
        'nurse_id' => $nurse->id,
        'shift_date' => $period->period_start,
        'code' => 'D',
    ]);

    LeaveRequest::create([
        'nurse_id' => $nurse->id,
        'type' => 'sick',
        'start_date' => '2025-01-12',
        'end_date' => '2025-01-14',
        'status' => 'pending',
    ]);

    $response = $this->get(route('public.roster.unit', $unit));

    $response->assertOk()
        ->assertSee('SICK LEAVE');
});

test('public roster page shows nurses on leave even without shifts', function () {
    $unit = Unit::factory()->create();
    $nurse = Nurse::factory()->create(['name' => 'On Leave Nurse', 'current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);

    $period = RosterPeriod::factory()->create([
        'unit_id' => $unit->id,
        'status' => 'submitted',
        'period_start' => '2025-01-10',
        'period_end' => '2025-01-20',
    ]);

    LeaveRequest::create([
        'nurse_id' => $nurse->id,
        'type' => 'annual',
        'start_date' => '2025-01-12',
        'end_date' => '2025-01-15',
        'status' => 'pending',
    ]);

    $response = $this->get(route('public.roster.unit', $unit));

    $response->assertOk()
        ->assertSee('On Leave Nurse')
        ->assertSee('ANNUAL LEAVE');
});

/*
 * --- OnDuty page (date filtering + access level scoping) ---
 */

test('on-duty page defaults to today when no date specified', function () {
    $page = new OnDuty;
    $page->boot(); // triggers date default

    expect($page->getDate()->toDateString())->toBe(Carbon::today()->toDateString());
});

test('on-duty page uses selected date for filtering', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'unit_id' => null]);
    $unit = Unit::factory()->create();
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id, 'grade' => 'Senior Nurse']);

    $period = RosterPeriod::factory()->create([
        'unit_id' => $unit->id,
        'created_by' => $admin->id,
        'status' => 'submitted',
        'period_start' => '2025-06-01',
        'period_end' => '2025-06-30',
    ]);

    Shift::create([
        'roster_period_id' => $period->id,
        'nurse_id' => $nurse->id,
        'shift_date' => '2025-06-15',
        'code' => 'D',
    ]);

    $this->actingAs($admin);

    $page = new OnDuty;
    $page->date = '2025-06-15';

    $results = $page->getOnDutyByUnit();

    expect($results->isNotEmpty())->toBeTrue()
        ->and($results->flatten()->pluck('nurse.grade')->first())->toBe('Senior Nurse');
});

test('on-duty filters only by selected date, not today', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'unit_id' => null]);
    $unit = Unit::factory()->create();
    $nurse = Nurse::factory()->create(['name' => 'Date Test Nurse', 'current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);

    $period = RosterPeriod::factory()->create([
        'unit_id' => $unit->id,
        'created_by' => $admin->id,
        'status' => 'submitted',
        'period_start' => '2025-06-01',
        'period_end' => '2025-06-30',
    ]);

    // Shift on a non-today date
    Shift::create([
        'roster_period_id' => $period->id,
        'nurse_id' => $nurse->id,
        'shift_date' => '2025-06-15',
        'code' => 'D',
    ]);

    $this->actingAs($admin);

    // Requesting a different date should not find the shift
    $page = new OnDuty;
    $page->date = '2025-06-20';

    $results = $page->getOnDutyByUnit();

    expect($results->isEmpty())->toBeTrue();
});

test('on-duty area_manager only sees their own unit', function () {
    $unitA = Unit::factory()->create(['name' => 'Unit A']);
    $unitB = Unit::factory()->create(['name' => 'Unit B']);
    $areaManager = User::factory()->create(['role' => User::ROLE_AREA_MANAGER, 'unit_id' => $unitA->id]);

    $nurseA = Nurse::factory()->create(['name' => 'Alice Nurse', 'current_unit_id' => $unitA->id, 'home_unit_id' => $unitA->id]);
    $nurseB = Nurse::factory()->create(['name' => 'Bob Nurse', 'current_unit_id' => $unitB->id, 'home_unit_id' => $unitB->id]);

    $periodA = RosterPeriod::factory()->create([
        'unit_id' => $unitA->id,
        'created_by' => $areaManager->id,
        'status' => 'submitted',
        'period_start' => '2025-06-01',
        'period_end' => '2025-06-30',
    ]);

    $periodB = RosterPeriod::factory()->create([
        'unit_id' => $unitB->id,
        'created_by' => $areaManager->id,
        'status' => 'submitted',
        'period_start' => '2025-06-01',
        'period_end' => '2025-06-30',
    ]);

    Shift::create(['roster_period_id' => $periodA->id, 'nurse_id' => $nurseA->id, 'shift_date' => '2025-06-15', 'code' => 'D']);
    Shift::create(['roster_period_id' => $periodB->id, 'nurse_id' => $nurseB->id, 'shift_date' => '2025-06-15', 'code' => 'D']);

    $this->actingAs($areaManager);

    $page = new OnDuty;
    $page->date = '2025-06-15';

    $results = $page->getOnDutyByUnit();

    expect($results->isNotEmpty())->toBeTrue()
        ->and($results->has('Unit A'))->toBeTrue()
        ->and($results->has('Unit B'))->toBeFalse();
});

test('on-duty admin sees all units', function () {
    $unitA = Unit::factory()->create(['name' => 'Unit A']);
    $unitB = Unit::factory()->create(['name' => 'Unit B']);
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'unit_id' => null]);

    $nurseA = Nurse::factory()->create(['name' => 'Alice Nurse', 'current_unit_id' => $unitA->id, 'home_unit_id' => $unitA->id]);
    $nurseB = Nurse::factory()->create(['name' => 'Bob Nurse', 'current_unit_id' => $unitB->id, 'home_unit_id' => $unitB->id]);

    $periodA = RosterPeriod::factory()->create([
        'unit_id' => $unitA->id,
        'created_by' => $admin->id,
        'status' => 'submitted',
        'period_start' => '2025-06-01',
        'period_end' => '2025-06-30',
    ]);

    $periodB = RosterPeriod::factory()->create([
        'unit_id' => $unitB->id,
        'created_by' => $admin->id,
        'status' => 'submitted',
        'period_start' => '2025-06-01',
        'period_end' => '2025-06-30',
    ]);

    Shift::create(['roster_period_id' => $periodA->id, 'nurse_id' => $nurseA->id, 'shift_date' => '2025-06-15', 'code' => 'D']);
    Shift::create(['roster_period_id' => $periodB->id, 'nurse_id' => $nurseB->id, 'shift_date' => '2025-06-15', 'code' => 'D']);

    $this->actingAs($admin);

    $page = new OnDuty;
    $page->date = '2025-06-15';

    $results = $page->getOnDutyByUnit();

    expect($results->has('Unit A'))->toBeTrue()
        ->and($results->has('Unit B'))->toBeTrue();
});
