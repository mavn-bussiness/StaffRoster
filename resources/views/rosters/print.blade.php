<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duty Roster — {{ $rosterPeriod->unit->name }} — {{ $rosterPeriod->period_start->format('F Y') }}</title>
    @vite(['resources/css/app.css'])
    @filamentStyles
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 8mm 8mm 8mm;
        }
        @media print {
            .no-print { display: none !important; }
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                background: #fff !important;
                color: #000 !important;
                font-size: 9px !important;
            }
            table {
                page-break-inside: auto;
                width: 100%;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
            .signature-card {
                page-break-inside: avoid;
            }
        }
        th.name-col, td.name-col {
            width: 155px;
            min-width: 155px;
            max-width: 175px;
        }
        th.day-col, td.day-col {
            width: 22px;
            min-width: 20px;
        }
        th.summary-col, td.summary-col {
            width: 48px;
            min-width: 44px;
        }
    </style>
</head>
<body class="bg-white text-gray-900 antialiased text-[9.5px]">

    {{-- Toolbar (hidden on print) --}}
    <div class="no-print flex items-center gap-3 px-6 py-3.5 border-b border-gray-200 bg-gray-50 sticky top-0 z-20 shadow-sm">
        <x-filament::button onclick="window.print()" icon="heroicon-o-printer" color="primary">
            Print Roster / Save PDF
        </x-filament::button>
        <x-filament::button
            tag="a"
            href="{{ route('filament.admin.resources.roster-overview.index') }}"
            color="gray"
            icon="heroicon-o-arrow-left"
        >
            Back to All Rosters
        </x-filament::button>
        <span class="ml-auto text-xs text-gray-500 font-medium">
            📄 For official hospital printouts, select <strong>A4 Landscape</strong> in your print settings.
        </span>
    </div>

    <div class="px-6 py-4">
        {{-- Hospital Header --}}
        <div class="border-b-2 border-gray-800 pb-3 mb-3">
            <div class="flex items-center justify-between gap-4">
                {{-- Prominent Hospital Logo --}}
                <div class="flex items-center gap-4">
                    <img src="{{ asset('hospital-logo.svg') }}" alt="Hospital Logo" class="w-36 h-36 shrink-0 object-contain drop-shadow-sm">
                    <div>
                        <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest leading-tight">
                            Republic of Uganda · Ministry of Health
                        </p>
                        <h1 class="text-base font-extrabold text-gray-950 uppercase tracking-wide leading-tight">
                            Mulago Specialized Women and Neonatal Hospital
                        </h1>
                        <p class="text-[11px] font-semibold text-gray-700 uppercase tracking-wider">
                            Nursing Administration
                        </p>
                    </div>
                </div>

                {{-- Document Badge & Meta --}}
                <div class="text-right">
                    <div class="inline-block bg-gray-900 text-white font-bold text-[11px] uppercase tracking-wider px-3 py-1 rounded">
                        Monthly Staff Duty Roster
                    </div>
                    <div class="mt-1.5 text-[10px] text-gray-600 space-y-0.5">
                        <p><strong>Month:</strong> <span class="text-gray-900 font-bold uppercase">{{ $rosterPeriod->period_start->format('F Y') }}</span></p>
                        <p><strong>Unit:</strong> <span class="text-gray-900 font-bold uppercase">{{ $rosterPeriod->unit->name }}</span></p>
                    </div>
                </div>
            </div>

            {{-- Sub-header details strip --}}
            <div class="mt-2.5 pt-2 border-t border-gray-200 flex items-center justify-between text-[9px] text-gray-600 flex-wrap gap-2">
                <div>
                    <strong>Period:</strong> {{ $rosterPeriod->period_start->format('d M Y') }} – {{ $rosterPeriod->period_end->format('d M Y') }}
                    <span class="mx-2 text-gray-300">|</span>
                    <strong>Unit Code:</strong> {{ $rosterPeriod->unit->code ?? '—' }}
                    <span class="mx-2 text-gray-300">|</span>
                    <strong>Target Min. Duty Days:</strong> <span class="font-bold text-gray-900">{{ $rosterPeriod->minimum_duty_days }} Days</span>
                </div>
                <div>
                    @if ($rosterPeriod->ward_meeting_date)
                        <strong>Ward Meeting Date:</strong> {{ $rosterPeriod->ward_meeting_date->format('d M Y') }}
                        <span class="mx-2 text-gray-300">|</span>
                    @endif
                    <strong>Status:</strong> <span class="uppercase font-bold text-gray-900">{{ $rosterPeriod->statusLabel() }}</span>
                    <span class="mx-2 text-gray-300">|</span>
                    <strong>Date Printed:</strong> {{ now()->format('d M Y, H:i') }}
                </div>
            </div>
        </div>

        {{-- Roster Grid Table --}}
        <div class="border border-gray-300 rounded-lg overflow-hidden">
            <table class="border-collapse w-full table-fixed text-[8.5px]">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-300">
                        <th class="name-col px-2.5 py-2 text-left font-extrabold text-gray-800 uppercase tracking-wide border-r border-gray-300">
                            Nurse / Staff Name
                        </th>
                        @foreach ($dates as $date)
                            @php $isWeekend = $date->isWeekend(); @endphp
                            <th class="day-col px-0.5 py-2 text-center border-r border-gray-200 {{ $isWeekend ? 'bg-blue-50/80 font-extrabold' : 'font-extrabold' }}">
                                <div class="text-gray-600 text-[7.5px] font-bold leading-none">{{ $date->format('D')[0] }}</div>
                                <div class="font-extrabold text-gray-900 text-[9px] leading-tight">{{ $date->format('d') }}</div>
                            </th>
                        @endforeach
                        <th class="summary-col px-1 py-2 text-center font-extrabold text-gray-800 uppercase text-[7.5px] border-l border-gray-300 bg-gray-100">
                            Duty (D)
                        </th>
                        <th class="summary-col px-1 py-2 text-center font-extrabold text-gray-800 uppercase text-[7.5px] border-l border-gray-200 bg-gray-100">
                            Off (O)
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($nurses as $nurse)
                        @php
                            $nurseShifts = $shiftsByNurseAndDate[$nurse->id] ?? collect();
                            $dutyCount   = collect($nurseShifts)->filter(fn($s) => in_array($s->code, ['D', 'E', 'N']))->count();
                            $offCount    = collect($nurseShifts)->filter(fn($s) => $s->code === 'O')->count();
                            $meetsMin    = $dutyCount >= $rosterPeriod->minimum_duty_days;
                            $isInCharge  = $nurse->grade === 'In-Charge';

                            $nurseLeaveSegments = $leaveSegments[$nurse->id] ?? [];
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
                        @endphp
                        <tr class="{{ $isInCharge ? 'bg-amber-50/40' : 'hover:bg-gray-50/50' }}">
                            <td class="name-col px-2.5 py-2 text-gray-900 border-r border-gray-300 whitespace-nowrap overflow-hidden text-ellipsis">
                                <span class="font-extrabold text-gray-950 text-[9.5px]">{{ $nurse->name }}</span>
                                @if ($nurse->grade)
                                    <span class="text-gray-500 font-bold text-[8px] ml-1">({{ $nurse->grade }})</span>
                                @endif
                                @if ($nurse->employment_type === 'contract' || $nurse->employment_type === 'Contract')
                                    <span class="text-[7px] bg-amber-100 text-amber-900 px-1 py-0.2 rounded font-bold ml-0.5">C</span>
                                @endif
                            </td>
                            @foreach ($dates as $date)
                                @php
                                    $dateStr = $date->toDateString();
                                    $leaveInfo = $leaveByDate[$dateStr] ?? null;
                                @endphp
                                @if ($leaveInfo && ! $leaveInfo['isStart'])
                                    @continue
                                @endif
                                @if ($leaveInfo)
                                    @php $leaveLabel = strtoupper(($leaveTypeLabels[$leaveInfo['type']] ?? ucfirst($leaveInfo['type'])).' LEAVE'); @endphp
                                    <td colspan="{{ $leaveInfo['span'] }}" class="day-col px-0.5 py-1.5 text-center border-r border-gray-200 bg-pink-50/70" title="{{ $leaveLabel }}">
                                        <span class="inline-flex items-center justify-center w-full h-6 rounded text-[9px] font-bold bg-pink-100 text-pink-900 px-1 whitespace-nowrap">
                                            {{ $leaveLabel }}
                                        </span>
                                    </td>
                                @else
                                    @php
                                        $shift   = $nurseShifts[$dateStr] ?? null;
                                        $code    = $shift?->code ?? '';
                                        $isWknd  = $date->isWeekend();
                                        $pillClass = match($code) {
                                            'D' => 'bg-blue-100 text-blue-900 font-extrabold',
                                            'E' => 'bg-amber-100 text-amber-900 font-extrabold',
                                            'N' => 'bg-indigo-100 text-indigo-900 font-extrabold',
                                            'O' => 'bg-gray-100 text-gray-700 font-bold',
                                            'L' => 'bg-pink-100 text-pink-900 font-extrabold',
                                            default => 'text-gray-300',
                                        };
                                    @endphp
                                    <td class="day-col px-0.5 py-1.5 text-center border-r border-gray-200 {{ $isWknd ? 'bg-blue-50/30' : '' }}">
                                        @if ($code)
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded text-[8.5px] {{ $pillClass }}">
                                                {{ $code }}
                                            </span>
                                        @else
                                            <span class="text-gray-300 text-[8px]">—</span>
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                            <td class="summary-col px-1 py-1.5 text-center border-l border-gray-300 font-extrabold {{ $meetsMin ? 'text-green-800' : 'text-red-700' }}">
                                {{ $dutyCount }}
                            </td>
                            <td class="summary-col px-1 py-1.5 text-center border-l border-gray-200 font-bold text-gray-700">
                                {{ $offCount }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="99" class="p-6 text-center text-gray-500 text-xs">
                                No nurses registered for this unit.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($nurses->isNotEmpty())
                    @php
                        $shiftRows = collect([['code' => 'D', 'label' => 'Day']]);
                        if ($rosterPeriod->unit->has_evening_shift) {
                            $shiftRows->push(['code' => 'E', 'label' => 'Evening']);
                        }
                        if ($rosterPeriod->unit->has_night_shift) {
                            $shiftRows->push(['code' => 'N', 'label' => 'Night']);
                        }
                    @endphp
                    <tfoot>
                        @foreach ($shiftRows as $shiftRow)
                            @php $rowTotal = 0; @endphp
                            <tr class="bg-gray-100 border-t-2 border-gray-300 font-extrabold">
                                <td class="name-col px-2.5 py-2 text-gray-800 uppercase tracking-wider text-[8px] border-r border-gray-300">
                                    {{ $shiftRow['label'] }} Totals
                                </td>
                                @foreach ($dates as $date)
                                    @php
                                        $count = collect($nurses)->filter(function ($n) use ($shiftsByNurseAndDate, $date, $shiftRow) {
                                            $s = $shiftsByNurseAndDate[$n->id][$date->toDateString()] ?? null;
                                            return $s && $s->code === $shiftRow['code'];
                                        })->count();
                                        $rowTotal += $count;
                                    @endphp
                                    <td class="day-col px-0.5 py-2 text-center border-r border-gray-200 {{ $date->isWeekend() ? 'bg-blue-50/60' : '' }}">
                                        <span class="text-[8.5px] {{ $count > 0 ? 'text-gray-900 font-extrabold' : 'text-gray-400' }}">
                                            {{ $count }}
                                        </span>
                                    </td>
                                @endforeach
                                <td colspan="2" class="summary-col px-1 py-2 text-center border-l border-gray-300 text-gray-900 text-[8px] font-extrabold">
                                    Total: {{ $rowTotal }}
                                </td>
                            </tr>
                        @endforeach
                    </tfoot>
                @endif
            </table>
        </div>

        {{-- Legend --}}
        <div class="flex items-center gap-4 mt-2.5 pt-2 border-t border-gray-200 text-[8px] text-gray-600">
            <span class="font-bold uppercase tracking-wider text-gray-700">Legend:</span>
            <span class="inline-flex items-center gap-1">
                <span class="w-4 h-4 rounded flex items-center justify-center font-bold bg-blue-100 text-blue-900">D</span>
                Day Shift
            </span>
            @if ($rosterPeriod->unit->has_evening_shift)
                <span class="inline-flex items-center gap-1">
                    <span class="w-4 h-4 rounded flex items-center justify-center font-bold bg-amber-100 text-amber-900">E</span>
                    Evening Shift
                </span>
            @endif
            @if ($rosterPeriod->unit->has_night_shift)
                <span class="inline-flex items-center gap-1">
                    <span class="w-4 h-4 rounded flex items-center justify-center font-bold bg-indigo-100 text-indigo-900">N</span>
                    Night Shift
                </span>
            @endif
            <span class="inline-flex items-center gap-1">
                <span class="w-4 h-4 rounded flex items-center justify-center font-bold bg-gray-100 text-gray-700">O</span>
                Off Duty
            </span>
            <span class="inline-flex items-center gap-1">
                <span class="w-4 h-4 rounded flex items-center justify-center font-bold bg-pink-100 text-pink-900">L</span>
                On Leave
            </span>
            <span class="ml-auto text-[7.5px] text-gray-400">
                Staff Roster Management System · Mulago Specialized Women & Neonatal Hospital
            </span>
        </div>

        {{-- Official Signatures / Sign-Off Section --}}
        <div class="signature-card mt-5 pt-3 border-t-2 border-gray-800">
            <div class="grid grid-cols-1 gap-6 text-[9px] max-w-md">

                {{-- Compiled by: In-Charge of the Unit --}}
                <div class="border border-gray-300 rounded p-2.5 bg-gray-50/50">
                    <p class="text-[8px] font-bold uppercase tracking-wider text-gray-500 mb-1">
                        Compiled by:
                    </p>
                    <p class="font-extrabold text-[10px] text-gray-950">
                        {{ $inCharge?->name ?? ($rosterPeriod->createdBy?->name ?? 'Nurse In-Charge') }}
                    </p>
                </div>

            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', () => {
            // Optional auto print if ?autoprint=1 is in query
            if (new URLSearchParams(window.location.search).get('autoprint') === '1') {
                window.print();
            }
        });
    </script>
</body>
</html>
