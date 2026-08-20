<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'has_evening_shift', 'has_night_shift'];

    protected $casts = [
        'has_evening_shift' => 'boolean',
        'has_night_shift' => 'boolean',
    ];

    public function nurses(): HasMany
    {
        return $this->hasMany(Nurse::class, 'current_unit_id');
    }

    public function rosterPeriods(): HasMany
    {
        return $this->hasMany(RosterPeriod::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Ensure in-charge user(s) for this unit are automatically registered as nurses.
     */
    public function syncInChargeNurse(): void
    {
        $inCharges = User::where('unit_id', $this->id)
            ->where('role', User::ROLE_IN_CHARGE)
            ->get();

        foreach ($inCharges as $inCharge) {
            Nurse::firstOrCreate(
                [
                    'name' => $inCharge->name,
                    'current_unit_id' => $this->id,
                ],
                [
                    'grade' => 'In-Charge',
                    'employment_type' => 'Permanent',
                    'home_unit_id' => $this->id,
                    'status' => 'active',
                    'date_joined' => $inCharge->created_at?->toDateString() ?? now()->toDateString(),
                ]
            );
        }
    }
}
