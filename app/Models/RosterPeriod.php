<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RosterPeriod extends Model
{
    use HasFactory;

    // Workflow: draft → agreed → under_review → pending_approval → approved → submitted
    //                                                            ↑ revision_requested (back to draft)
    public const STATUS_DRAFT = 'draft';

    public const STATUS_AGREED = 'agreed';

    public const STATUS_UNDER_REVIEW = 'under_review';       // sent to area manager

    public const STATUS_PENDING_APPROVAL = 'pending_approval';   // forwarded to head nursing

    public const STATUS_REVISION_REQUESTED = 'revision_requested'; // HN requests changes

    public const STATUS_APPROVED = 'approved';           // HN approves

    public const STATUS_SUBMITTED = 'submitted';          // printed & forwarded to ED/MoH

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_AGREED,
        self::STATUS_UNDER_REVIEW,
        self::STATUS_PENDING_APPROVAL,
        self::STATUS_REVISION_REQUESTED,
        self::STATUS_APPROVED,
        self::STATUS_SUBMITTED,
    ];

    protected $fillable = [
        'unit_id', 'created_by', 'period_start', 'period_end',
        'status', 'minimum_duty_days', 'ward_meeting_date', 'review_note',
        'reviewed_by', 'approved_by',
        'agreed_at', 'submitted_for_review_at', 'forwarded_to_hn_at',
        'approved_at', 'submitted_to_moh_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'ward_meeting_date' => 'date',
        'agreed_at' => 'datetime',
        'submitted_for_review_at' => 'datetime',
        'forwarded_to_hn_at' => 'datetime',
        'approved_at' => 'datetime',
        'submitted_to_moh_at' => 'datetime',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    public function offDayRequests(): HasMany
    {
        return $this->hasMany(OffDayRequest::class);
    }

    /** Count on-duty shifts (D/E/N) for a given nurse in this period. */
    public function dutyDaysFor(int $nurseId): int
    {
        return $this->shifts()
            ->where('nurse_id', $nurseId)
            ->whereIn('code', ['D', 'E', 'N'])
            ->count();
    }

    /** True if the roster is still editable by the in-charge. */
    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REVISION_REQUESTED]);
    }

    /** Statuses that allow rolling back to a previous step. */
    public const ROLLBACK_ALLOWED_STATUSES = [
        self::STATUS_AGREED,
        self::STATUS_UNDER_REVIEW,
        self::STATUS_PENDING_APPROVAL,
        self::STATUS_REVISION_REQUESTED,
        self::STATUS_APPROVED,
        self::STATUS_SUBMITTED,
    ];

    /** Return valid previous steps for rollback, ordered from closest to farthest. */
    public function previousSteps(): array
    {
        $order = [
            self::STATUS_DRAFT,
            self::STATUS_AGREED,
            self::STATUS_UNDER_REVIEW,
            self::STATUS_PENDING_APPROVAL,
            self::STATUS_REVISION_REQUESTED,
            self::STATUS_APPROVED,
            self::STATUS_SUBMITTED,
        ];

        $currentIdx = array_search($this->status, $order, true);
        if ($currentIdx === false || $currentIdx === 0) {
            return [];
        }

        $previous = array_slice($order, 0, $currentIdx);

        return array_reverse($previous);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_AGREED => 'Agreed',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_PENDING_APPROVAL => 'Pending Approval',
            self::STATUS_REVISION_REQUESTED => 'Revision Requested',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_SUBMITTED => 'Submitted',
            default => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_AGREED => 'info',
            self::STATUS_UNDER_REVIEW => 'warning',
            self::STATUS_PENDING_APPROVAL => 'warning',
            self::STATUS_REVISION_REQUESTED => 'danger',
            self::STATUS_APPROVED => 'success',
            self::STATUS_SUBMITTED => 'success',
            default => 'gray',
        };
    }
}
