<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nurse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'grade', 'employment_type',
        'home_unit_id', 'current_unit_id', 'date_joined', 'status',
    ];

    protected $casts = [
        'date_joined' => 'date',
    ];

    public function homeUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'home_unit_id');
    }

    public function currentUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'current_unit_id');
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    public function offDayRequests(): HasMany
    {
        return $this->hasMany(OffDayRequest::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class);
    }

    /**
     * Move this nurse to a new unit and record the transfer.
     */
    public function transferTo(Unit $unit, string $startDate, ?string $endDate = null, ?string $reason = null): Transfer
    {
        return $this->transfers()->create([
            'from_unit_id' => $this->current_unit_id,
            'to_unit_id' => $unit->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'reason' => $reason,
            'status' => Transfer::STATUS_PENDING,
        ]);
    }
}
