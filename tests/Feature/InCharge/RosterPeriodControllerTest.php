<?php

use App\Filament\Resources\RosterPeriods\Pages\CreateRosterPeriod;
use App\Filament\Resources\RosterPeriods\Pages\ListRosterPeriods;
use App\Filament\Resources\RosterPeriods\Pages\RosterGrid;
use App\Models\Nurse;
use App\Models\RosterPeriod;
use App\Models\Shift;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Livewire;

function rosterInCharge(): array
{
    $unit = Unit::factory()->create();
    $user = User::factory()->create(['role' => User::ROLE_IN_CHARGE, 'unit_id' => $unit->id]);

    return [$user, $unit];
}

test('in_charge can see only their unit roster periods', function () {
    [$user, $unit] = rosterInCharge();
    $mine = RosterPeriod::factory()->create(['unit_id' => $unit->id, 'created_by' => $user->id]);
    $other = RosterPeriod::factory()->create(); // different unit

    $this->actingAs($user);

    Livewire::test(ListRosterPeriods::class)
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$other]);
});

test('in_charge can create a roster period for their unit', function () {
    [$user] = rosterInCharge();
    $nextMonth = Carbon::now()->addMonth()->startOfMonth()->format('Y-m');

    $this->actingAs($user);

    Livewire::test(CreateRosterPeriod::class)
        ->fillForm([
            'roster_month' => $nextMonth,
            'minimum_duty_days' => 20,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('roster_periods', [
        'unit_id' => $user->unit_id,
        'status' => 'draft',
    ]);
});

test('in_charge is automatically added as a nurse in the unit when drafting', function () {
    [$user, $unit] = rosterInCharge();
    $period = RosterPeriod::factory()->create([
        'unit_id' => $unit->id,
        'created_by' => $user->id,
        'status' => 'draft',
    ]);

    $this->actingAs($user);

    $component = Livewire::test(RosterGrid::class, ['record' => $period]);
    $nurses = $component->instance()->getNurses();

    expect($nurses->pluck('name')->contains($user->name))->toBeTrue();
    $inChargeNurse = $nurses->firstWhere('name', $user->name);
    expect($inChargeNurse->grade)->toBe('In-Charge');
    expect($inChargeNurse->current_unit_id)->toBe($unit->id);
});

test('in_charge can mark a draft roster as agreed', function () {
    [$user, $unit] = rosterInCharge();
    $period = RosterPeriod::factory()->create([
        'unit_id' => $unit->id,
        'created_by' => $user->id,
        'status' => 'draft',
    ]);

    $this->actingAs($user);

    Livewire::test(RosterGrid::class, ['record' => $period])
        ->callAction('agree')
        ->assertHasNoActionErrors();

    expect($period->fresh()->status)->toBe('agreed');
});

test('in_charge can submit an agreed roster for review', function () {
    [$user, $unit] = rosterInCharge();
    $period = RosterPeriod::factory()->create([
        'unit_id' => $unit->id,
        'created_by' => $user->id,
        'status' => 'agreed',
        'period_start' => '2026-09-01',
        'period_end' => '2026-09-30',
    ]);

    $this->actingAs($user);

    Livewire::test(RosterGrid::class, ['record' => $period])
        ->callAction('submitForReview', data: [
            'ward_meeting_date' => '2026-09-05',
        ])
        ->assertHasNoActionErrors();

    expect($period->fresh()->status)->toBe('under_review');
});

test('area_manager can request adjustment on roster under review', function () {
    $unit = Unit::factory()->create();
    $inCharge = User::factory()->create(['role' => User::ROLE_IN_CHARGE, 'unit_id' => $unit->id]);
    $areaManager = User::factory()->create(['role' => User::ROLE_AREA_MANAGER, 'unit_id' => $unit->id]);
    $period = RosterPeriod::factory()->create([
        'unit_id' => $unit->id,
        'created_by' => $inCharge->id,
        'status' => RosterPeriod::STATUS_UNDER_REVIEW,
        'period_start' => '2026-09-01',
        'period_end' => '2026-09-30',
    ]);

    $this->actingAs($areaManager);

    Livewire::test(RosterGrid::class, ['record' => $period])
        ->callAction('requestAdjustment', data: [
            'review_note' => 'Please balance night shifts in week 2.',
        ])
        ->assertHasNoActionErrors();

    $fresh = $period->fresh();
    expect($fresh->status)->toBe(RosterPeriod::STATUS_REVISION_REQUESTED);
    expect($fresh->review_note)->toBe('Please balance night shifts in week 2.');
    expect($fresh->reviewed_by)->toBe($areaManager->id);
});

test('in_charge can edit and resubmit roster after revision requested', function () {
    [$inCharge, $unit] = rosterInCharge();
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);
    $period = RosterPeriod::factory()->create([
        'unit_id' => $unit->id,
        'created_by' => $inCharge->id,
        'status' => RosterPeriod::STATUS_REVISION_REQUESTED,
        'period_start' => '2026-09-01',
        'period_end' => '2026-09-30',
        'review_note' => 'Please adjust schedule.',
    ]);

    $this->actingAs($inCharge);

    // In-charge edits shifts and saves
    Livewire::test(RosterGrid::class, ['record' => $period])
        ->set('shifts.'.$nurse->id.'.2026-09-01', 'D')
        ->call('saveShifts')
        ->assertHasNoErrors()
        ->callAction('resubmit', data: [
            'ward_meeting_date' => '2026-09-05',
        ])
        ->assertHasNoActionErrors();

    $fresh = $period->fresh();
    expect($fresh->status)->toBe(RosterPeriod::STATUS_UNDER_REVIEW);
});

test('area_manager can forward roster to head nursing', function () {
    $unit = Unit::factory()->create();
    $areaManager = User::factory()->create(['role' => User::ROLE_AREA_MANAGER, 'unit_id' => $unit->id]);
    $period = RosterPeriod::factory()->create([
        'unit_id' => $unit->id,
        'status' => RosterPeriod::STATUS_UNDER_REVIEW,
        'period_start' => '2026-09-01',
        'period_end' => '2026-09-30',
    ]);

    $this->actingAs($areaManager);

    Livewire::test(RosterGrid::class, ['record' => $period])
        ->callAction('forwardToHN')
        ->assertHasNoActionErrors();

    $fresh = $period->fresh();
    expect($fresh->status)->toBe(RosterPeriod::STATUS_PENDING_APPROVAL);
    expect($fresh->forwarded_to_hn_at)->not->toBeNull();
});

test('head_nursing can request revision from pending approval', function () {
    $headNursing = User::factory()->create(['role' => User::ROLE_HEAD_NURSING]);
    $period = RosterPeriod::factory()->create([
        'status' => RosterPeriod::STATUS_PENDING_APPROVAL,
        'period_start' => '2026-09-01',
        'period_end' => '2026-09-30',
    ]);

    $this->actingAs($headNursing);

    Livewire::test(RosterGrid::class, ['record' => $period])
        ->callAction('requestRevision', data: [
            'review_note' => 'Night shift coverage inadequate on weekends.',
        ])
        ->assertHasNoActionErrors();

    $fresh = $period->fresh();
    expect($fresh->status)->toBe(RosterPeriod::STATUS_REVISION_REQUESTED);
    expect($fresh->review_note)->toBe('Night shift coverage inadequate on weekends.');
    expect($fresh->reviewed_by)->toBe($headNursing->id);
});

test('head_nursing can approve roster pending approval', function () {
    $headNursing = User::factory()->create(['role' => User::ROLE_HEAD_NURSING]);
    $period = RosterPeriod::factory()->create([
        'status' => RosterPeriod::STATUS_PENDING_APPROVAL,
        'period_start' => '2026-09-01',
        'period_end' => '2026-09-30',
    ]);

    $this->actingAs($headNursing);

    Livewire::test(RosterGrid::class, ['record' => $period])
        ->callAction('approve')
        ->assertHasNoActionErrors();

    $fresh = $period->fresh();
    expect($fresh->status)->toBe(RosterPeriod::STATUS_APPROVED);
    expect($fresh->approved_by)->toBe($headNursing->id);
    expect($fresh->approved_at)->not->toBeNull();
});

test('head_nursing or admin can mark approved roster as submitted to MoH', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $period = RosterPeriod::factory()->create([
        'status' => RosterPeriod::STATUS_APPROVED,
        'period_start' => '2026-09-01',
        'period_end' => '2026-09-30',
    ]);

    $this->actingAs($admin);

    Livewire::test(RosterGrid::class, ['record' => $period])
        ->callAction('submit')
        ->assertHasNoActionErrors();

    $fresh = $period->fresh();
    expect($fresh->status)->toBe(RosterPeriod::STATUS_SUBMITTED);
    expect($fresh->submitted_to_moh_at)->not->toBeNull();
});

test('in_charge cannot access another unit roster grid', function () {
    [$user] = rosterInCharge();
    $other = RosterPeriod::factory()->create(); // different unit

    $this->actingAs($user);

    $this->get(route('filament.admin.resources.roster-periods.grid', $other))
        ->assertForbidden();
});

test('in_charge can save shifts on a draft roster and clearing shifts deletes them', function () {
    [$user, $unit] = rosterInCharge();
    $period = RosterPeriod::factory()->create([
        'unit_id' => $unit->id,
        'created_by' => $user->id,
        'status' => 'draft',
        'period_start' => '2025-08-01',
        'period_end' => '2025-08-31',
    ]);
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);

    $this->actingAs($user);

    Livewire::test(RosterGrid::class, ['record' => $period])
        ->set('shifts.'.$nurse->id.'.2025-08-01', 'D')
        ->set('shifts.'.$nurse->id.'.2025-08-02', 'O')
        ->call('saveShifts')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('shifts', [
        'roster_period_id' => $period->id,
        'nurse_id' => $nurse->id,
        'code' => 'D',
    ]);

    // Now clear the shift and save again
    Livewire::test(RosterGrid::class, ['record' => $period])
        ->set('shifts.'.$nurse->id.'.2025-08-01', '')
        ->call('saveShifts')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('shifts', [
        'roster_period_id' => $period->id,
        'nurse_id' => $nurse->id,
        'shift_date' => '2025-08-01',
    ]);
});

test('in_charge cannot save shifts on a non-draft and non-revision roster', function () {
    [$user, $unit] = rosterInCharge();
    $period = RosterPeriod::factory()->create([
        'unit_id' => $unit->id,
        'created_by' => $user->id,
        'status' => 'agreed',
    ]);
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);

    $this->actingAs($user);

    Livewire::test(RosterGrid::class, ['record' => $period])
        ->set('shifts.'.$nurse->id.'.2025-08-01', 'D')
        ->call('saveShifts')
        ->assertForbidden();
});

test('admin can edit shifts on any roster status', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $unit = Unit::factory()->create();
    $period = RosterPeriod::factory()->create([
        'unit_id' => $unit->id,
        'status' => 'approved',
        'period_start' => '2025-08-01',
        'period_end' => '2025-08-31',
    ]);
    $nurse = Nurse::factory()->create(['current_unit_id' => $unit->id, 'home_unit_id' => $unit->id]);

    $this->actingAs($admin);

    Livewire::test(RosterGrid::class, ['record' => $period])
        ->set('shifts.'.$nurse->id.'.2025-08-01', 'D')
        ->call('saveShifts')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('shifts', [
        'roster_period_id' => $period->id,
        'nurse_id' => $nurse->id,
        'shift_date' => '2025-08-01',
        'code' => 'D',
    ]);
});

test('admin can rollback approved roster to draft', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $period = RosterPeriod::factory()->create([
        'status' => RosterPeriod::STATUS_APPROVED,
        'period_start' => '2025-08-01',
        'period_end' => '2025-08-31',
        'approved_at' => now(),
        'approved_by' => $admin->id,
    ]);

    $this->actingAs($admin);

    Livewire::test(RosterGrid::class, ['record' => $period])
        ->callAction('rollback', data: ['step' => RosterPeriod::STATUS_DRAFT])
        ->assertHasNoActionErrors();

    $fresh = $period->fresh();
    expect($fresh->status)->toBe(RosterPeriod::STATUS_DRAFT);
    expect($fresh->approved_at)->toBeNull();
    expect($fresh->approved_by)->toBeNull();
});

test('admin can rollback submitted roster to approved', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $period = RosterPeriod::factory()->create([
        'status' => RosterPeriod::STATUS_SUBMITTED,
        'period_start' => '2025-08-01',
        'period_end' => '2025-08-31',
        'submitted_to_moh_at' => now(),
    ]);

    $this->actingAs($admin);

    Livewire::test(RosterGrid::class, ['record' => $period])
        ->callAction('rollback', data: ['step' => RosterPeriod::STATUS_APPROVED])
        ->assertHasNoActionErrors();

    $fresh = $period->fresh();
    expect($fresh->status)->toBe(RosterPeriod::STATUS_APPROVED);
    expect($fresh->submitted_to_moh_at)->toBeNull();
});

test('in_charge cannot rollback a roster', function () {
    [$user, $unit] = rosterInCharge();
    $period = RosterPeriod::factory()->create([
        'unit_id' => $unit->id,
        'status' => RosterPeriod::STATUS_APPROVED,
    ]);

    $this->actingAs($user);

    $component = Livewire::test(RosterGrid::class, ['record' => $period]);
    expect($component->instance()->canRollback())->toBeFalse();
});

test('area_manager cannot rollback a roster', function () {
    $areaManager = User::factory()->create(['role' => User::ROLE_AREA_MANAGER]);
    $period = RosterPeriod::factory()->create([
        'status' => RosterPeriod::STATUS_APPROVED,
    ]);

    $this->actingAs($areaManager);

    $component = Livewire::test(RosterGrid::class, ['record' => $period]);
    expect($component->instance()->canRollback())->toBeFalse();
});

test('head_nursing cannot rollback a roster', function () {
    $headNursing = User::factory()->create(['role' => User::ROLE_HEAD_NURSING]);
    $period = RosterPeriod::factory()->create([
        'status' => RosterPeriod::STATUS_APPROVED,
    ]);

    $this->actingAs($headNursing);

    $component = Livewire::test(RosterGrid::class, ['record' => $period]);
    expect($component->instance()->canRollback())->toBeFalse();
});
