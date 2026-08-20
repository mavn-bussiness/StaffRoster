<?php

use App\Models\Nurse;
use App\Models\Unit;

test('transferTo moves nurse to new unit and records transfer', function () {
    $fromUnit = Unit::factory()->create();
    $toUnit = Unit::factory()->create();
    $nurse = Nurse::factory()->create(['current_unit_id' => $fromUnit->id, 'home_unit_id' => $fromUnit->id]);

    $transfer = $nurse->transferTo($toUnit, '2025-09-01', '2025-12-31', 'Temporary cover');

    expect($nurse->fresh()->current_unit_id)->toBe($toUnit->id);
    expect($transfer->from_unit_id)->toBe($fromUnit->id);
    expect($transfer->to_unit_id)->toBe($toUnit->id);
    expect($transfer->reason)->toBe('Temporary cover');
});

test('transferTo with no end date sets end_date to null', function () {
    $fromUnit = Unit::factory()->create();
    $toUnit = Unit::factory()->create();
    $nurse = Nurse::factory()->create(['current_unit_id' => $fromUnit->id, 'home_unit_id' => $fromUnit->id]);

    $transfer = $nurse->transferTo($toUnit, '2025-09-01');

    expect($transfer->end_date)->toBeNull();
});
