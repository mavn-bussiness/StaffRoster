<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'unit_id'];

    protected $hidden = ['password', 'remember_token'];

    // Roles: admin | in_charge | area_manager | head_nursing
    public const ROLE_ADMIN = 'admin';

    public const ROLE_IN_CHARGE = 'in_charge';

    public const ROLE_AREA_MANAGER = 'area_manager';

    public const ROLE_HEAD_NURSING = 'head_nursing';

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isInCharge(): bool
    {
        return $this->role === self::ROLE_IN_CHARGE;
    }

    /**
     * Roles that can see every unit's data.
     * admin, head_nursing, and area_manager all see all units;
     * in_charge is scoped to their own unit only.
     */
    public function seesAllUnits(): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMIN,
            self::ROLE_HEAD_NURSING,
            self::ROLE_AREA_MANAGER,
        ], true);
    }

    /**
     * Scope a Unit query to what this user is allowed to see.
     * Usage: Unit::visibleTo($user)->get();
     */
    public function scopeVisibleUnitsFor($query, User $user)
    {
        if ($user->seesAllUnits()) {
            return $query;
        }

        return $query->where('id', $user->unit_id);
    }

    protected static function booted(): void
    {
        static::saved(function (User $user) {
            if ($user->role === self::ROLE_IN_CHARGE && $user->unit_id) {
                $user->unit?->syncInChargeNurse();
            }
        });
    }
}
