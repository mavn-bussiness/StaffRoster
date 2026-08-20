<x-filament-panels::page>
    <style>
        :root {
            --rg-card-bg:       #fff;
            --rg-card-border:   #e5e7eb;
            --rg-th-bg:         #f8fafc;
            --rg-th-border:     #e5e7eb;
            --rg-th-color:      #6b7280;
            --rg-row-border:    #f1f5f9;
            --rg-row-hover:     #f8fafc;
            --rg-cell-border:   #f1f5f9;
            --rg-sticky-bg:     #fff;
            --rg-footer-bg:     #f8fafc;
            --rg-savebar-bg:    #fafafa;
            --rg-savebar-border:#f1f5f9;
            --rg-legend-bg:     #fff;
            --rg-legend-border: #f1f5f9;
            --rg-text-main:     #111827;
            --rg-text-sub:      #9ca3af;
            --rg-text-muted:    #6b7280;
            --rg-divider:       #e5e7eb;
            --rg-today-bg:      #eff6ff;
            --rg-today-color:   #2563eb;
            --rg-weekend-bg:    #fafafa;
            --rg-empty-color:   #e5e7eb;
            --rg-select-bg:     #f9fafb;
            --rg-select-border: #e5e7eb;
            --rg-select-empty:  #d1d5db;
            --rg-pill-D-bg:     #dbeafe; --rg-pill-D-color: #1e40af;
            --rg-pill-E-bg:     #fef3c7; --rg-pill-E-color: #92400e;
            --rg-pill-N-bg:     #e0e7ff; --rg-pill-N-color: #3730a3;
            --rg-pill-O-bg:     #f3f4f6; --rg-pill-O-color: #6b7280;
            --rg-ok-bg:         #dcfce7; --rg-ok-color:     #15803d;
            --rg-bad-bg:        #fee2e2; --rg-bad-color:    #b91c1c;
            --rg-ok-bar:        #22c55e; --rg-bad-bar:      #f87171;
            --rg-bar-track:     #f1f5f9;
            --rg-contract-bg:   #fef3c7; --rg-contract-color:#92400e;
            --rg-rev-bg:        #fef2f2; --rg-rev-border:   #fecaca;
            --rg-step-active-bg:#2563eb; --rg-step-active-c:#fff;    --rg-step-active-b:#2563eb;
            --rg-step-done-bg:  #dcfce7; --rg-step-done-c:  #16a34a; --rg-step-done-b:  #86efac;
            --rg-step-idle-bg:  #f9fafb; --rg-step-idle-c:  #d1d5db; --rg-step-idle-b:  #e5e7eb;
            --rg-step-rev-bg:   #fef2f2; --rg-step-rev-c:   #dc2626; --rg-step-rev-b:   #fca5a5;
            --rg-conn-done:     #86efac; --rg-conn-idle:    #e5e7eb;
            --rg-label-active:  #2563eb; --rg-label-done:   #16a34a; --rg-label-idle:   #9ca3af;
        }
        .dark {
            --rg-card-bg:       #1f2937;
            --rg-card-border:   #374151;
            --rg-th-bg:         #111827;
            --rg-th-border:     #374151;
            --rg-th-color:      #9ca3af;
            --rg-row-border:    #374151;
            --rg-row-hover:     #374151;
            --rg-cell-border:   #374151;
            --rg-sticky-bg:     #1f2937;
            --rg-footer-bg:     #111827;
            --rg-savebar-bg:    #111827;
            --rg-savebar-border:#374151;
            --rg-legend-bg:     #1f2937;
            --rg-legend-border: #374151;
            --rg-text-main:     #f3f4f6;
            --rg-text-sub:      #6b7280;
            --rg-text-muted:    #9ca3af;
            --rg-divider:       #374151;
            --rg-today-bg:      #19324f;
            --rg-today-color:   #60a5fa;
            --rg-weekend-bg:    #1a2230;
            --rg-empty-color:   #374151;
            --rg-select-bg:     #374151;
            --rg-select-border: #4b5563;
            --rg-select-empty:  #6b7280;
            --rg-pill-D-bg:     #1e3a5f; --rg-pill-D-color: #93c5fd;
            --rg-pill-E-bg:     #451a03; --rg-pill-E-color: #fcd34d;
            --rg-pill-N-bg:     #1e1b4b; --rg-pill-N-color: #a5b4fc;
            --rg-pill-O-bg:     #374151; --rg-pill-O-color: #9ca3af;
            --rg-ok-bg:         #14532d; --rg-ok-color:     #86efac;
            --rg-bad-bg:        #450a0a; --rg-bad-color:    #fca5a5;
            --rg-ok-bar:        #4ade80; --rg-bad-bar:      #f87171;
            --rg-bar-track:     #374151;
            --rg-contract-bg:   #451a03; --rg-contract-color:#fcd34d;
            --rg-rev-bg:        #450a0a; --rg-rev-border:   #7f1d1d;
            --rg-step-active-bg:#2563eb; --rg-step-active-c:#fff;    --rg-step-active-b:#2563eb;
            --rg-step-done-bg:  #14532d; --rg-step-done-c:  #86efac; --rg-step-done-b:  #166534;
            --rg-step-idle-bg:  #374151; --rg-step-idle-c:  #4b5563; --rg-step-idle-b:  #4b5563;
            --rg-step-rev-bg:   #450a0a; --rg-step-rev-c:   #fca5a5; --rg-step-rev-b:   #7f1d1d;
            --rg-conn-done:     #166534; --rg-conn-idle:    #374151;
            --rg-label-active:  #60a5fa; --rg-label-done:   #86efac; --rg-label-idle:   #4b5563;
        }
    </style>

    @php
        $nurses         = $this->getNurses();
        $dates          = collect(iterator_to_array($this->getDates()));
        $availableCodes = $this->getAvailableCodes();
        $isDraft        = $record->isEditable();
        $canEdit        = $this->canEdit();
        $leaveDates     = $this->leaveDates;
        $minDays        = $record->minimum_duty_days;
        $totalDays      = $dates->count();

        $pillStyle = [
            'D' => 'background:var(--rg-pill-D-bg);color:var(--rg-pill-D-color);',
            'E' => 'background:var(--rg-pill-E-bg);color:var(--rg-pill-E-color);',
            'N' => 'background:var(--rg-pill-N-bg);color:var(--rg-pill-N-color);',
            'O' => 'background:var(--rg-pill-O-bg);color:var(--rg-pill-O-color);',
            'L' => 'background:#fce7f3;color:#9d174d;',
        ];
        $codeLabels = ['D' => 'Day', 'E' => 'Evening', 'N' => 'Night', 'O' => 'Off'];
        $leaveTypeLabels = [
            'annual' => 'Annual',
            'sick' => 'Sick',
            'maternity' => 'Maternity',
            'compassionate' => 'Compassionate',
            'other' => 'Other',
        ];

        $steps = [
            'draft'            => ['label' => 'Draft',            'icon' => '✎'],
            'agreed'           => ['label' => 'Agreed',           'icon' => '✔'],
            'under_review'     => ['label' => 'Under Review',     'icon' => '⟳'],
            'pending_approval' => ['label' => 'Pending Approval', 'icon' => '⏳'],
            'approved'         => ['label' => 'Approved',         'icon' => '✔'],
            'submitted'        => ['label' => 'Submitted',        'icon' => '✉'],
        ];
        $statusOrder = array_keys($steps);
        $currentIdx  = array_search($record->status === 'revision_requested' ? 'draft' : $record->status, $statusOrder);
    @endphp

    {{-- ── Revision Banner ─────────────────────────────────────── --}}
    @if ($record->status === 'revision_requested' && $record->review_note)
        @php
            $reviewerRole = match ($record->reviewedBy?->role) {
                'area_manager' => 'Area Manager',
                'head_nursing' => 'Head Nursing',
                'admin'        => 'Administrator',
                default        => 'Supervisor',
            };
            $reviewerName = $record->reviewedBy?->name;
        @endphp
        <div style="background:var(--rg-rev-bg);border:1px solid var(--rg-rev-border);border-radius:0.75rem;padding:1rem 1.25rem;display:flex;gap:0.75rem;margin-bottom:1rem;">
            <svg style="width:1.25rem;height:1.25rem;color:#ef4444;flex-shrink:0;margin-top:0.1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <div>
                <p style="font-size:0.875rem;font-weight:600;color:#b91c1c;margin:0 0 0.2rem;">
                    Revision Requested by {{ $reviewerRole }}{{ $reviewerName ? ' (' . $reviewerName . ')' : '' }}
                </p>
                <p style="font-size:0.875rem;color:#dc2626;margin:0;">{{ $record->review_note }}</p>
            </div>
        </div>
    @endif

    {{-- ── Period Info Card ─────────────────────────────────────── --}}
    <div style="background:var(--rg-card-bg);border:1px solid var(--rg-card-border);border-radius:0.875rem;padding:1.25rem 1.5rem;margin-bottom:1rem;box-shadow:0 1px 4px rgba(0,0,0,0.05);">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;">

            <div>
                <p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--rg-text-muted);margin:0 0 0.25rem;">
                    {{ $record->unit->name }}
                </p>
                <h2 style="font-size:1.1rem;font-weight:700;color:var(--rg-text-main);margin:0 0 0.35rem;">
                    {{ $record->period_start->format('d M Y') }} &ndash; {{ $record->period_end->format('d M Y') }}
                </h2>
                <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
                    <x-filament::badge :color="$record->statusColor()">{{ $record->statusLabel() }}</x-filament::badge>
                    <span style="font-size:0.75rem;color:var(--rg-text-muted);">
                        {{ $totalDays }} days &nbsp;·&nbsp; Min. duty: <strong>{{ $minDays }} days</strong>
                        &nbsp;·&nbsp; {{ $nurses->count() }} nurses
                    </span>
                    @if ($record->ward_meeting_date)
                        <span style="font-size:0.75rem;color:#7c3aed;">
                            📅 Ward meeting: {{ $record->ward_meeting_date->format('d M Y') }}
                        </span>
                    @endif
                    @if ($record->approved_at)
                        <span style="font-size:0.75rem;color:#16a34a;">
                            ✓ Approved by {{ $record->approvedBy?->name ?? '—' }} on {{ $record->approved_at->format('d M Y') }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Workflow steps --}}
            <div style="display:flex;align-items:center;">
                @foreach ($steps as $key => $step)
                    @php
                        $idx        = array_search($key, $statusOrder);
                        $done       = $idx < $currentIdx;
                        $active     = $idx === $currentIdx;
                        $isRevision = $record->status === 'revision_requested' && $key === 'draft';

                        if ($isRevision) {
                            $dotStyle   = 'background:var(--rg-step-rev-bg);color:var(--rg-step-rev-c);border:2px solid var(--rg-step-rev-b);';
                            $labelStyle = 'color:var(--rg-label-idle);';
                        } elseif ($active) {
                            $dotStyle   = 'background:var(--rg-step-active-bg);color:var(--rg-step-active-c);border:2px solid var(--rg-step-active-b);';
                            $labelStyle = 'color:var(--rg-label-active);font-weight:700;';
                        } elseif ($done) {
                            $dotStyle   = 'background:var(--rg-step-done-bg);color:var(--rg-step-done-c);border:2px solid var(--rg-step-done-b);';
                            $labelStyle = 'color:var(--rg-label-done);';
                        } else {
                            $dotStyle   = 'background:var(--rg-step-idle-bg);color:var(--rg-step-idle-c);border:2px solid var(--rg-step-idle-b);';
                            $labelStyle = 'color:var(--rg-label-idle);';
                        }
                        $connStyle = 'background:' . ($done ? 'var(--rg-conn-done)' : 'var(--rg-conn-idle)') . ';';
                    @endphp
                    <div style="display:flex;align-items:center;">
                        <div style="display:flex;flex-direction:column;align-items:center;gap:0.2rem;padding:0 0.5rem;">
                            <div style="width:1.75rem;height:1.75rem;border-radius:9999px;display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;{{ $dotStyle }}">
                                {{ $step['icon'] }}
                            </div>
                            <span style="font-size:0.6rem;white-space:nowrap;font-weight:500;{{ $labelStyle }}">{{ $step['label'] }}</span>
                        </div>
                        @if (!$loop->last)
                            <div style="width:1.5rem;height:2px;margin-bottom:1rem;flex-shrink:0;{{ $connStyle }}"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Roster Grid ──────────────────────────────────────────── --}}
    <div style="background:var(--rg-card-bg);border:1px solid var(--rg-card-border);border-radius:0.875rem;box-shadow:0 1px 4px rgba(0,0,0,0.05);overflow:hidden;">
        <form wire:submit="saveShifts">
            <div style="overflow:auto;max-height:75vh;">
                <table style="border-collapse:separate;border-spacing:0;width:100%;font-size:0.75rem;">
                    <thead>
                        <tr style="background:var(--rg-th-bg);">
                            <th style="position:sticky;left:0;top:0;z-index:30;background:var(--rg-th-bg);padding:0.75rem 1rem;text-align:left;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--rg-th-color);min-width:180px;border-right:2px solid var(--rg-th-border);border-bottom:2px solid var(--rg-th-border);white-space:nowrap;">
                                Nurse / Grade
                            </th>
                            @foreach ($dates as $date)
                                @php $isWeekend = $date->isWeekend(); $isToday = $date->isToday(); @endphp
                                <th style="position:sticky;top:0;z-index:20;padding:0.5rem 0.125rem;text-align:center;min-width:38px;border-right:1px solid var(--rg-cell-border);border-bottom:2px solid var(--rg-th-border);background:var(--rg-th-bg);{{ $isToday ? 'background:var(--rg-today-bg);' : ($isWeekend ? 'background:var(--rg-weekend-bg);' : '') }}">
                                    <div style="font-size:0.6rem;color:var(--rg-text-sub);font-weight:500;line-height:1;">{{ $date->format('D') }}</div>
                                    <div style="font-size:0.8rem;font-weight:700;color:{{ $isToday ? 'var(--rg-today-color)' : 'var(--rg-text-main)' }};line-height:1.4;">{{ $date->format('d') }}</div>
                                    @if ($isToday)
                                        <div style="width:4px;height:4px;border-radius:9999px;background:var(--rg-today-color);margin:1px auto 0;"></div>
                                    @endif
                                </th>
                            @endforeach
                            <th style="position:sticky;top:0;z-index:20;padding:0.5rem 0.625rem;text-align:center;min-width:60px;border-left:2px solid var(--rg-th-border);border-bottom:2px solid var(--rg-th-border);background:var(--rg-th-bg);font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--rg-th-color);white-space:nowrap;">
                                Duty Days
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($nurses as $nurse)
                                @php
                                    $nurseLeaveSegments = $leaveDates[$nurse->id] ?? [];
                                    $leaveByDate = [];
                                    foreach ($nurseLeaveSegments as $seg) {
                                        $segStart = $seg['start'];
                                        $segEnd = $seg['end'];
                                        $segPeriod = \Carbon\CarbonPeriod::create($segStart, $segEnd);
                                        $segSpan = $segPeriod->count();
                                        foreach ($segPeriod as $day) {
                                            $ds = $day->toDateString();
                                            $leaveByDate[$ds] = [
                                                'type' => $seg['type'],
                                                'isStart' => $ds === $segStart->toDateString(),
                                                'span' => $segSpan,
                                            ];
                                        }
                                    }
                                    $dutyCount  = collect($shifts[$nurse->id] ?? [])->filter(fn($c) => in_array($c, ['D','E','N']))->count();
                                    $meetsMin   = $dutyCount >= $minDays;
                                    $pct        = $minDays > 0 ? min(100, round($dutyCount / $minDays * 100)) : 100;
                                @endphp
                            <tr style="border-bottom:1px solid var(--rg-row-border);" onmouseover="this.style.background='var(--rg-row-hover)'" onmouseout="this.style.background=''">
                                <td style="position:sticky;left:0;z-index:5;background:var(--rg-sticky-bg);padding:0.75rem 1rem;border-right:2px solid var(--rg-th-border);white-space:nowrap;">
                                    <span style="font-weight:800;color:var(--rg-text-main);font-size:0.8rem;">{{ $nurse->name }}</span>
                                    @if ($nurse->grade)
                                        <span style="font-size:0.7rem;color:var(--rg-text-sub);margin-left:0.375rem;font-weight:400;">{{ $nurse->grade }}</span>
                                    @endif
                                    @if ($nurse->employment_type === 'contract')
                                        <span style="font-size:0.6rem;background:var(--rg-contract-bg);color:var(--rg-contract-color);padding:0.1rem 0.35rem;border-radius:9999px;margin-left:0.25rem;font-weight:600;">C</span>
                                    @endif
                                </td>
                                @foreach ($dates as $date)
                                    @php
                                        $key = $date->toDateString();
                                        $leaveInfo = $leaveByDate[$key] ?? null;
                                    @endphp
                                    @if ($leaveInfo && ! $leaveInfo['isStart'])
                                        @continue
                                    @endif
                                    @if ($leaveInfo)
                                        @php $leaveLabel = strtoupper(($leaveTypeLabels[$leaveInfo['type']] ?? ucfirst($leaveInfo['type'])).' LEAVE'); @endphp
                                        <td colspan="{{ $leaveInfo['span'] }}" style="padding:0.25rem 0.5rem;text-align:center;border-right:1px solid var(--rg-cell-border);background:#fdf2f8;" title="{{ $leaveLabel }}">
                                            <span style="display:inline-flex;align-items:center;justify-content:center;width:100%;min-height:1.75rem;border-radius:0.375rem;font-weight:700;font-size:0.85rem;letter-spacing:0.01em;background:#fce7f3;color:#9d174d;padding:0.2rem 0.5rem;white-space:nowrap;">
                                                {{ $leaveLabel }}
                                            </span>
                                        </td>
                                    @else
                                        @php
                                            $code      = $shifts[$nurse->id][$key] ?? '';
                                            $isWeekend = $date->isWeekend();
                                            $isToday   = $date->isToday();
                                            $cellBg    = $isToday ? 'background:var(--rg-today-bg);' : ($isWeekend ? 'background:var(--rg-weekend-bg);' : '');
                                        @endphp
                                        <td style="padding:0.5rem 0.125rem;text-align:center;border-right:1px solid var(--rg-cell-border);{{ $cellBg }}">
                                            @if ($canEdit)
                                                <select
                                                    wire:model="shifts.{{ $nurse->id }}.{{ $key }}"
                                                    style="width:2.5rem;height:2.25rem;text-align:center;font-size:0.7rem;font-weight:700;border-radius:0.375rem;border:1px solid var(--rg-select-border);cursor:pointer;appearance:none;-webkit-appearance:none;outline:none;{{ $code && isset($pillStyle[$code]) ? $pillStyle[$code] : 'background:var(--rg-select-bg);color:var(--rg-select-empty);' }}"
                                                    onchange="
                                                        const s={
                                                            'D':'background:var(--rg-pill-D-bg);color:var(--rg-pill-D-color);',
                                                            'E':'background:var(--rg-pill-E-bg);color:var(--rg-pill-E-color);',
                                                            'N':'background:var(--rg-pill-N-bg);color:var(--rg-pill-N-color);',
                                                            'O':'background:var(--rg-pill-O-bg);color:var(--rg-pill-O-color);'
                                                        };
                                                        const base='width:2.5rem;height:2.25rem;text-align:center;font-size:0.7rem;font-weight:700;border-radius:0.375rem;border:1px solid var(--rg-select-border);cursor:pointer;appearance:none;-webkit-appearance:none;outline:none;';
                                                        this.style.cssText=base+(s[this.value]||'background:var(--rg-select-bg);color:var(--rg-select-empty);');
                                                    "
                                                >
                                                    <option value="">—</option>
                                                    @foreach ($availableCodes as $c)
                                                        <option value="{{ $c }}" {{ $code === $c ? 'selected' : '' }}>{{ $c }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                @if ($code)
                                                    <span style="display:inline-flex;align-items:center;justify-content:center;width:2rem;height:2rem;border-radius:0.375rem;font-weight:700;font-size:0.7rem;{{ $pillStyle[$code] ?? 'background:var(--rg-pill-O-bg);color:var(--rg-pill-O-color);' }}">{{ $code }}</span>
                                                @else
                                                    <span style="color:var(--rg-empty-color);font-size:0.75rem;">—</span>
                                                @endif
                                            @endif
                                        </td>
                                    @endif
                                @endforeach
                                <td style="padding:0.5rem 0.625rem;text-align:center;border-left:2px solid var(--rg-th-border);">
                                    <div style="display:flex;flex-direction:column;align-items:center;gap:0.2rem;">
                                        <span style="display:inline-flex;align-items:center;justify-content:center;width:2rem;height:2rem;border-radius:0.375rem;font-weight:700;font-size:0.75rem;background:{{ $meetsMin ? 'var(--rg-ok-bg)' : 'var(--rg-bad-bg)' }};color:{{ $meetsMin ? 'var(--rg-ok-color)' : 'var(--rg-bad-color)' }};">{{ $dutyCount }}</span>
                                        <div style="width:2rem;height:3px;background:var(--rg-bar-track);border-radius:9999px;overflow:hidden;">
                                            <div style="height:100%;width:{{ $pct }}%;background:{{ $meetsMin ? 'var(--rg-ok-bar)' : 'var(--rg-bad-bar)' }};border-radius:9999px;transition:width 0.3s;"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="99" style="padding:3rem;text-align:center;color:var(--rg-text-muted);font-size:0.875rem;">
                                    No active nurses assigned to this unit.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($nurses->isNotEmpty())
                        @php
                            $shiftTotalRows = collect([['code' => 'D', 'label' => 'Day']]);
                            if ($record->unit->has_evening_shift) {
                                $shiftTotalRows->push(['code' => 'E', 'label' => 'Evening']);
                            }
                            if ($record->unit->has_night_shift) {
                                $shiftTotalRows->push(['code' => 'N', 'label' => 'Night']);
                            }
                        @endphp
                        <tfoot>
                            @foreach ($shiftTotalRows as $st)
                                @php
                                    $rowTotals = [];
                                    foreach ($dates as $date) {
                                        $rowTotals[$date->toDateString()] = collect($nurses)->filter(
                                            fn ($n) => ($shifts[$n->id][$date->toDateString()] ?? null) === $st['code']
                                        )->count();
                                    }
                                    $grand = array_sum($rowTotals);
                                @endphp
                                <tr style="background:var(--rg-footer-bg);">
                                    <td style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:700;color:var(--rg-th-color);text-transform:uppercase;letter-spacing:0.06em;border-right:2px solid var(--rg-th-border);border-top:2px solid var(--rg-th-border);position:sticky;left:0;bottom:0;z-index:25;background:var(--rg-footer-bg);">
                                        {{ $st['label'] }} Total
                                    </td>
                                    @foreach ($dates as $date)
                                        <td style="position:sticky;bottom:0;z-index:15;padding:0.5rem 0.125rem;text-align:center;border-right:1px solid var(--rg-th-border);border-top:2px solid var(--rg-th-border);background:var(--rg-footer-bg);{{ $date->isToday() ? 'background:var(--rg-today-bg);' : ($date->isWeekend() ? 'background:var(--rg-weekend-bg);' : '') }}">
                                            @if ($rowTotals[$date->toDateString()] > 0)
                                                <span style="font-size:0.7rem;font-weight:700;color:var(--rg-text-main);">{{ $rowTotals[$date->toDateString()] }}</span>
                                            @else
                                                <span style="color:var(--rg-empty-color);font-size:0.7rem;">0</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td style="position:sticky;bottom:0;z-index:15;padding:0.75rem 0.625rem;text-align:center;border-left:2px solid var(--rg-th-border);border-top:2px solid var(--rg-th-border);background:var(--rg-footer-bg);">
                                        <span style="font-size:0.7rem;font-weight:700;color:var(--rg-text-main);">{{ $grand }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tfoot>
                    @endif
                </table>
            </div>
            @if ($canEdit && $nurses->isNotEmpty())
                <div style="padding:0.75rem 1.25rem;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--rg-savebar-border);background:var(--rg-savebar-bg);">
                    <span style="font-size:0.75rem;color:var(--rg-text-muted);">
                        @if ($record->status === 'revision_requested')
                            Save your changes below, then click <strong>Resubmit to Area Manager</strong> in the top-right header to submit the revised roster.
                        @else
                            Changes are saved per session — click Save to persist.
                        @endif
                    </span>
                    <x-filament::button type="submit" icon="heroicon-o-check">Save Roster</x-filament::button>
                </div>
            @endif
        </form>
    </div>

    {{-- ── Legend ───────────────────────────────────────────────── --}}
    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:0.75rem 1.25rem;margin-top:0.875rem;padding:0.75rem 1rem;background:var(--rg-legend-bg);border:1px solid var(--rg-legend-border);border-radius:0.75rem;">
        <span style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--rg-text-muted);">Legend</span>
        @foreach ($codeLabels as $code => $label)
            @if (in_array($code, $availableCodes))
                <span style="display:inline-flex;align-items:center;gap:0.375rem;font-size:0.75rem;color:var(--rg-text-muted);">
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:1.5rem;height:1.5rem;border-radius:0.3rem;font-weight:700;font-size:0.65rem;{{ $pillStyle[$code] }}">{{ $code }}</span>
                    {{ $label }}
                </span>
            @endif
        @endforeach
        <span style="width:1px;height:1rem;background:var(--rg-divider);"></span>
        <span style="display:inline-flex;align-items:center;gap:0.375rem;font-size:0.75rem;color:var(--rg-text-muted);">
            <span style="display:inline-flex;align-items:center;justify-content:center;width:1.5rem;height:1.5rem;border-radius:0.3rem;font-weight:700;font-size:0.65rem;background:var(--rg-ok-bg);color:var(--rg-ok-color);">✓</span>
            Meets min. ({{ $minDays }}d)
        </span>
        <span style="display:inline-flex;align-items:center;gap:0.375rem;font-size:0.75rem;color:var(--rg-text-muted);">
            <span style="display:inline-flex;align-items:center;justify-content:center;width:1.5rem;height:1.5rem;border-radius:0.3rem;font-weight:700;font-size:0.65rem;background:var(--rg-bad-bg);color:var(--rg-bad-color);">!</span>
            Below min.
        </span>
        <span style="display:inline-flex;align-items:center;gap:0.375rem;font-size:0.75rem;color:var(--rg-text-muted);">
            <span style="display:inline-flex;align-items:center;justify-content:center;width:1.5rem;height:1.5rem;border-radius:0.3rem;font-weight:700;font-size:0.65rem;background:var(--rg-contract-bg);color:var(--rg-contract-color);">C</span>
            Contract nurse
        </span>
        <span style="display:inline-flex;align-items:center;gap:0.375rem;font-size:0.75rem;color:var(--rg-text-muted);">
            <span style="display:inline-flex;align-items:center;justify-content:center;width:1.5rem;height:1.5rem;border-radius:0.3rem;font-weight:700;font-size:0.65rem;background:var(--rg-today-bg);color:var(--rg-today-color);">•</span>
            Today
        </span>
        <span style="display:inline-flex;align-items:center;gap:0.375rem;font-size:0.75rem;color:var(--rg-text-muted);">
            <span style="display:inline-flex;align-items:center;justify-content:center;width:1.5rem;height:1.5rem;border-radius:0.3rem;font-weight:700;font-size:0.65rem;background:var(--rg-ok-bg);color:var(--rg-ok-color);">✓</span>
            Meets min. ({{ $minDays }}d)
        </span>
    </div>

</x-filament-panels::page>
