<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OffDayRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'nurse_id', 'roster_period_id', 'requested_date',
        'reason', 'written_request_date', 'status',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'written_request_date' => 'date',
    ];

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(Nurse::class);
    }

    public function rosterPeriod(): BelongsTo
    {
        return $this->belongsTo(RosterPeriod::class);
    }
}
