<?php

namespace App\Filament\Resources\RosterPeriods\Pages;

use App\Filament\Resources\RosterPeriods\RosterPeriodResource;
use App\Models\LeaveRequest;
use App\Models\Nurse;
use App\Models\RosterPeriod;
use App\Models\Shift;
use Carbon\CarbonPeriod;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class RosterGrid extends Page
{
    protected static string $resource = RosterPeriodResource::class;

    public function getView(): string
    {
        return 'filament.resources.roster-periods.pages.roster-grid';
    }

    public RosterPeriod $record;

    public array $shifts = [];

    /** nurse_id → list of approved leave segments [['start'=>Carbon,'end'=>Carbon,'type'=>string]] */
    public array $leaveDates = [];

    public function mount(RosterPeriod $record): void
    {
        $user = auth()->user();

        if ($user->role === 'in_charge') {
            abort_unless($record->unit_id === $user->unit_id, 403);
        }

        $this->record = $record;
        $this->record->unit?->syncInChargeNurse();

        foreach (Shift::where('roster_period_id', $record->id)->get() as $shift) {
            $this->shifts[$shift->nurse_id][$shift->shift_date->toDateString()] = $shift->code;
        }

        // Build leave date map for all nurses in this period.
        // Leave is never individually approved — it is just a reason a nurse
        // will not work, reflected automatically once logged by the in-charge.
        // Only explicitly rejected leave (if any) is ignored.
        $nurseIds = Nurse::where('current_unit_id', $record->unit_id)->pluck('id');
        LeaveRequest::where('status', '!=', 'rejected')
            ->whereIn('nurse_id', $nurseIds)
            ->where('start_date', '<=', $record->period_end)
            ->where('end_date', '>=', $record->period_start)
            ->get()
            ->each(function ($leave) use ($record) {
                $start = $leave->start_date->max($record->period_start);
                $end = $leave->end_date->min($record->period_end);
                $this->leaveDates[$leave->nurse_id][] = [
                    'start' => $start,
                    'end' => $end,
                    'type' => $leave->type,
                ];
            });
    }

    public function getTitle(): string|Htmlable
    {
        return $this->record->unit->name.' — '.
            $this->record->period_start->format('d M').' – '.
            $this->record->period_end->format('d M Y');
    }

    public function getNurses()
    {
        $this->record->unit?->syncInChargeNurse();

        return Nurse::where('current_unit_id', $this->record->unit_id)
            ->where('status', '!=', 'inactive')
            ->orderByRaw("CASE WHEN grade = 'In-Charge' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();
    }

    public function getDates()
    {
        return CarbonPeriod::create($this->record->period_start, $this->record->period_end);
    }

    public function getAvailableCodes(): array
    {
        $codes = ['D', 'O'];
        if ($this->record->unit->has_evening_shift) {
            $codes[] = 'E';
        }
        if ($this->record->unit->has_night_shift) {
            $codes[] = 'N';
        }

        return $codes;
    }

    public function canEdit(): bool
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'in_charge' && $this->record->isEditable();
    }

    public function canRollback(): bool
    {
        $user = auth()->user();

        return $user->isAdmin() && in_array($this->record->status, RosterPeriod::ROLLBACK_ALLOWED_STATUSES);
    }

    public function rollbackToStep(string $step): void
    {
        abort_unless($this->canRollback(), 403);

        $previousSteps = $this->record->previousSteps();
        abort_unless(in_array($step, $previousSteps), 422);

        $updates = ['status' => $step];

        if ($step === RosterPeriod::STATUS_DRAFT) {
            $updates = array_merge($updates, [
                'agreed_at' => null,
                'submitted_for_review_at' => null,
                'forwarded_to_hn_at' => null,
                'approved_at' => null,
                'submitted_to_moh_at' => null,
                'review_note' => null,
                'reviewed_by' => null,
                'approved_by' => null,
            ]);
        } elseif ($step === RosterPeriod::STATUS_AGREED) {
            $updates = array_merge($updates, [
                'submitted_for_review_at' => null,
                'forwarded_to_hn_at' => null,
                'approved_at' => null,
                'submitted_to_moh_at' => null,
                'review_note' => null,
                'reviewed_by' => null,
                'approved_by' => null,
            ]);
        } elseif ($step === RosterPeriod::STATUS_UNDER_REVIEW) {
            $updates = array_merge($updates, [
                'forwarded_to_hn_at' => null,
                'approved_at' => null,
                'submitted_to_moh_at' => null,
                'review_note' => null,
                'reviewed_by' => null,
                'approved_by' => null,
            ]);
        } elseif ($step === RosterPeriod::STATUS_PENDING_APPROVAL) {
            $updates = array_merge($updates, [
                'approved_at' => null,
                'submitted_to_moh_at' => null,
                'review_note' => null,
                'reviewed_by' => null,
                'approved_by' => null,
            ]);
        } elseif ($step === RosterPeriod::STATUS_REVISION_REQUESTED) {
            $updates = array_merge($updates, [
                'approved_at' => null,
                'submitted_to_moh_at' => null,
                'review_note' => null,
                'reviewed_by' => null,
                'approved_by' => null,
            ]);
        } elseif ($step === RosterPeriod::STATUS_APPROVED) {
            $updates = array_merge($updates, [
                'submitted_to_moh_at' => null,
            ]);
        }

        $this->record->update($updates);
        Notification::make()->title('Roster rolled back to '.match ($step) {
            RosterPeriod::STATUS_DRAFT => 'Draft',
            RosterPeriod::STATUS_AGREED => 'Agreed',
            RosterPeriod::STATUS_UNDER_REVIEW => 'Under Review',
            RosterPeriod::STATUS_PENDING_APPROVAL => 'Pending Approval',
            RosterPeriod::STATUS_REVISION_REQUESTED => 'Revision Requested',
            RosterPeriod::STATUS_APPROVED => 'Approved',
            default => ucfirst($step),
        }.'.')->warning()->send();
        $this->record->refresh();
    }

    public function saveShifts(): void
    {
        abort_unless($this->canEdit(), 403);

        $validNurseIds = Nurse::where('current_unit_id', $this->record->unit_id)->pluck('id');

        $rows = [];
        $cleared = [];

        foreach ($this->shifts as $nurseId => $days) {
            if (! $validNurseIds->contains((int) $nurseId)) {
                continue;
            }
            $onLeave = [];
            foreach ($this->leaveDates[$nurseId] ?? [] as $segment) {
                foreach (CarbonPeriod::create($segment['start'], $segment['end']) as $day) {
                    $onLeave[] = $day->toDateString();
                }
            }
            $onLeave = array_unique($onLeave);
            foreach ($days as $date => $code) {
                if ($code && (! in_array($code, ['D', 'E', 'N']) || ! in_array($date, $onLeave))) {
                    $rows[] = [
                        'roster_period_id' => $this->record->id,
                        'nurse_id' => (int) $nurseId,
                        'shift_date' => $date,
                        'code' => $code,
                    ];
                } else {
                    $cleared[] = [
                        'nurse_id' => (int) $nurseId,
                        'shift_date' => $date,
                    ];
                }
            }
        }

        if ($rows) {
            Shift::upsert($rows, ['roster_period_id', 'nurse_id', 'shift_date'], ['code']);
        }

        foreach ($cleared as $item) {
            Shift::where('roster_period_id', $this->record->id)
                ->where('nurse_id', $item['nurse_id'])
                ->where('shift_date', $item['shift_date'])
                ->delete();
        }

        Notification::make()->title('Roster saved.')->success()->send();
    }

    protected function getHeaderActions(): array
    {
        $role = auth()->user()->role;
        $status = $this->record->status;

        return array_filter([

            // ── IN-CHARGE ACTIONS ──────────────────────────────────────────

            // draft → agreed
            ($role === 'in_charge' || $role === 'admin') && $status === RosterPeriod::STATUS_DRAFT
                ? Action::make('agree')
                    ->label('Mark as Agreed')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalDescription('Mark this roster as agreed following the ward meeting.')
                    ->action(function () {
                        $this->record->update([
                            'status' => RosterPeriod::STATUS_AGREED,
                            'agreed_at' => now(),
                            'review_note' => null,
                        ]);
                        Notification::make()->title('Roster marked as agreed.')->success()->send();
                        $this->record->refresh();
                    })
                : null,

            // agreed → under_review (submit to area manager)
            ($role === 'in_charge' || $role === 'admin') && $status === RosterPeriod::STATUS_AGREED
                ? Action::make('submitForReview')
                    ->label('Submit for Review')
                    ->color('warning')
                    ->form([
                        DatePicker::make('ward_meeting_date')
                            ->label('Ward Meeting Date')
                            ->required()
                            ->default($this->record->ward_meeting_date)
                            ->afterOrEqual($this->record->period_start->toDateString())
                            ->beforeOrEqual($this->record->period_end->toDateString()),
                    ])
                    ->fillForm(['ward_meeting_date' => $this->record->ward_meeting_date])
                    ->modalDescription('Set the ward unit meeting date, then submit to the Area Manager for review.')
                    ->action(function (array $data) {
                        $this->record->update([
                            'status' => RosterPeriod::STATUS_UNDER_REVIEW,
                            'ward_meeting_date' => $data['ward_meeting_date'],
                            'submitted_for_review_at' => now(),
                        ]);
                        Notification::make()->title('Roster submitted to Area Manager for review.')->success()->send();
                        $this->record->refresh();
                    })
                : null,

            // revision_requested → under_review (resubmit to area manager after changes)
            ($role === 'in_charge' || $role === 'admin') && $status === RosterPeriod::STATUS_REVISION_REQUESTED
                ? Action::make('resubmit')
                    ->label('Resubmit to Area Manager')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->form([
                        DatePicker::make('ward_meeting_date')
                            ->label('Ward Meeting Date')
                            ->required()
                            ->default($this->record->ward_meeting_date)
                            ->afterOrEqual($this->record->period_start->toDateString())
                            ->beforeOrEqual($this->record->period_end->toDateString()),
                    ])
                    ->fillForm(['ward_meeting_date' => $this->record->ward_meeting_date])
                    ->requiresConfirmation()
                    ->modalHeading('Resubmit Roster for Review')
                    ->modalDescription('Update the ward meeting date if needed, then submit your updated roster to the Area Manager for review.')
                    ->action(function (array $data) {
                        $this->record->update([
                            'status' => RosterPeriod::STATUS_UNDER_REVIEW,
                            'ward_meeting_date' => $data['ward_meeting_date'],
                            'submitted_for_review_at' => now(),
                        ]);
                        Notification::make()->title('Roster resubmitted to Area Manager for review.')->success()->send();
                        $this->record->refresh();
                    })
                : null,

            // ── AREA MANAGER ACTIONS ───────────────────────────────────────

            // under_review → revision_requested (area manager requests adjustment)
            in_array($role, ['area_manager', 'admin']) && $status === RosterPeriod::STATUS_UNDER_REVIEW
                ? Action::make('requestAdjustment')
                    ->label('Request Adjustment')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->form([
                        TextInput::make('review_note')
                            ->label('Adjustment Notes')
                            ->placeholder('Describe what needs to be adjusted...')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $this->record->update([
                            'status' => RosterPeriod::STATUS_REVISION_REQUESTED,
                            'review_note' => $data['review_note'],
                            'reviewed_by' => auth()->id(),
                        ]);
                        Notification::make()->title('Adjustment requested. In-charge has been notified.')->warning()->send();
                        $this->record->refresh();
                    })
                : null,

            // under_review → pending_approval (forward to head nursing)
            in_array($role, ['area_manager', 'admin']) && $status === RosterPeriod::STATUS_UNDER_REVIEW
                ? Action::make('forwardToHN')
                    ->label('Forward to Head Nursing')
                    ->icon('heroicon-o-arrow-right')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription('Forward this roster to the Head Nursing Officer for final approval.')
                    ->action(function () {
                        $this->record->update([
                            'status' => RosterPeriod::STATUS_PENDING_APPROVAL,
                            'reviewed_by' => auth()->id(),
                            'forwarded_to_hn_at' => now(),
                        ]);
                        Notification::make()->title('Roster forwarded to Head Nursing.')->success()->send();
                        $this->record->refresh();
                    })
                : null,

            // ── HEAD NURSING ACTIONS ───────────────────────────────────────

            // pending_approval → approved
            in_array($role, ['head_nursing', 'admin']) && $status === RosterPeriod::STATUS_PENDING_APPROVAL
                ? Action::make('approve')
                    ->label('Approve Roster')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Approve this roster. It will be ready to print and submit.')
                    ->action(function () {
                        $this->record->update([
                            'status' => RosterPeriod::STATUS_APPROVED,
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        Notification::make()->title('Roster approved.')->success()->send();
                        $this->record->refresh();
                    })
                : null,

            // pending_approval → revision_requested
            in_array($role, ['head_nursing', 'admin']) && $status === RosterPeriod::STATUS_PENDING_APPROVAL
                ? Action::make('requestRevision')
                    ->label('Request Revision')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->form([
                        TextInput::make('review_note')
                            ->label('Revision Notes')
                            ->placeholder('Describe what needs to be changed...')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $this->record->update([
                            'status' => RosterPeriod::STATUS_REVISION_REQUESTED,
                            'review_note' => $data['review_note'],
                            'reviewed_by' => auth()->id(),
                        ]);
                        Notification::make()->title('Revision requested. In-charge has been notified.')->warning()->send();
                        $this->record->refresh();
                    })
                : null,

            // ── ADMIN / HEAD NURSING: submit after approval ────────────────

            in_array($role, ['admin', 'head_nursing']) && $status === RosterPeriod::STATUS_APPROVED
                ? Action::make('submit')
                    ->label('Mark as Submitted to MoH')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function () {
                        $this->record->update(['status' => RosterPeriod::STATUS_SUBMITTED, 'submitted_to_moh_at' => now()]);
                        Notification::make()->title('Roster marked as submitted to MoH.')->success()->send();
                        $this->record->refresh();
                    })
                : null,

            // Admin rollback
            $this->canRollback()
                ? Action::make('rollback')
                    ->label('Rollback')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->form([
                        Select::make('step')
                            ->label('Rollback to step')
                            ->options(collect($this->record->previousSteps())->mapWithKeys(fn ($s) => [$s => match ($s) {
                                RosterPeriod::STATUS_DRAFT => 'Draft',
                                RosterPeriod::STATUS_AGREED => 'Agreed',
                                RosterPeriod::STATUS_UNDER_REVIEW => 'Under Review',
                                RosterPeriod::STATUS_PENDING_APPROVAL => 'Pending Approval',
                                RosterPeriod::STATUS_REVISION_REQUESTED => 'Revision Requested',
                                RosterPeriod::STATUS_APPROVED => 'Approved',
                                default => ucfirst($s),
                            }])->all())
                            ->required(),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Rollback Roster')
                    ->modalDescription('This will move the roster back to the selected step and clear related timestamps. This action should be used with caution.')
                    ->action(function (array $data) {
                        $this->rollbackToStep($data['step']);
                    })
                : null,

            // Print — available once approved
            in_array($status, [RosterPeriod::STATUS_APPROVED, RosterPeriod::STATUS_SUBMITTED])
                ? Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(route('rosters.print', $this->record))
                    ->openUrlInNewTab()
                : null,
        ]);
    }
}
