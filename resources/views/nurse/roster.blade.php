<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $nurse->name }} — My Roster</title>
    @fonts
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-gray-100 p-4 sm:p-8">
    <div class="max-w-4xl mx-auto">

        {{-- Header --}}
        <div class="mb-6 flex items-center gap-4">
            <img src="{{ asset('hospital-logo.svg') }}" alt="MSWNH Logo" class="w-14 h-14 shrink-0 object-contain">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Mulago Specialized Women and Neonatal Hospital</p>
                <h1 class="text-xl font-semibold text-gray-900">{{ $nurse->name }}</h1>
                <p class="text-sm text-gray-500">
                    {{ $nurse->currentUnit->name ?? '—' }}
                    @if ($nurse->grade) · {{ $nurse->grade }} @endif
                </p>
            </div>
        </div>

        @forelse ($periodsData as $item)
            @php
                $period  = $item['period'];
                $dates   = $item['dates'];
                $byNurse = $item['byNurse'];
                $leaveSegments = $item['leaveSegments'] ?? [];
                $nurseShifts = $byNurse[$nurse->id] ?? collect();

                $codeLabels = ['D' => 'Day', 'E' => 'Evening', 'N' => 'Night', 'O' => 'Off'];
                $pillColors = [
                    'D' => 'bg-blue-100 text-blue-800',
                    'E' => 'bg-amber-100 text-amber-800',
                    'N' => 'bg-indigo-100 text-indigo-800',
                    'O' => 'bg-gray-100 text-gray-500',
                ];
                $leaveTypeLabels = [
                    'annual' => 'Annual',
                    'sick' => 'Sick',
                    'maternity' => 'Maternity',
                    'compassionate' => 'Compassionate',
                    'other' => 'Other',
                ];

                // Build leave map: date => [type, isStart, span]
                $leaveByDate = [];
                foreach ($leaveSegments as $seg) {
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

            <div class="card mb-5 overflow-hidden">
                <div class="card-header">
                    <span class="font-medium text-gray-800 text-sm">
                        {{ $period->period_start->format('d M Y') }} – {{ $period->period_end->format('d M Y') }}
                    </span>
                    <span class="badge {{ $period->status === 'submitted' ? 'badge-green' : 'badge-blue' }}">
                        {{ $period->statusLabel() }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                @foreach ($dates as $date)
                                    <th class="px-2 py-2 text-center font-medium text-gray-500 border-r border-gray-100 last:border-r-0 min-w-[44px] {{ $date->isWeekend() ? 'bg-blue-50' : '' }}">
                                        <div class="text-[10px] text-gray-400">{{ $date->format('D') }}</div>
                                        <div class="text-gray-800 font-bold text-[13px] leading-tight">{{ $date->format('d') }}</div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach ($dates as $date)
                                    @php
                                        $dateStr = $date->toDateString();
                                        $leaveInfo = $leaveByDate[$dateStr] ?? null;
                                    @endphp
                                    @if ($leaveInfo && ! $leaveInfo['isStart'])
                                        @continue
                                    @endif
                                    @php
                                        $leaveLabel = $leaveInfo ? strtoupper(($leaveTypeLabels[$leaveInfo['type']] ?? ucfirst($leaveInfo['type'])).' LEAVE') : null;
                                    @endphp
                                    @if ($leaveInfo)
                                        <td colspan="{{ $leaveInfo['span'] }}" class="px-1 py-2 text-center border-r border-gray-100 last:border-r-0 bg-pink-50/70" title="{{ $leaveLabel }}">
                                            <span class="inline-flex items-center justify-center font-bold text-xs bg-pink-100 text-pink-900 px-1 py-0.5 rounded-md whitespace-nowrap">
                                                {{ $leaveLabel }}
                                            </span>
                                        </td>
                                    @else
                                        @php $shift = $nurseShifts[$dateStr] ?? null; @endphp
                                        <td class="px-1 py-2 text-center border-r border-gray-100 last:border-r-0 {{ $date->isWeekend() ? 'bg-blue-50/30' : '' }}">
                                            @if ($shift)
                                                <span class="inline-flex w-8 h-8 rounded-md items-center justify-center font-bold text-sm {{ $pillColors[$shift->code] ?? 'bg-gray-100 text-gray-500' }}"
                                                      title="{{ $codeLabels[$shift->code] ?? $shift->code }}">
                                                    {{ $shift->code }}
                                                </span>
                                            @else
                                                <span class="text-gray-300">—</span>
                                            @endif
                                        </td>
                                    @endif
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-2.5 border-t border-gray-100 flex flex-wrap gap-3">
                    @foreach ($codeLabels as $code => $label)
                        <span class="inline-flex items-center gap-1.5 text-xs text-gray-500">
                            <span class="w-6 h-6 rounded-md flex items-center justify-center font-bold text-[11px] {{ $pillColors[$code] }}">{{ $code }}</span>
                            {{ $label }}
                        </span>
                    @endforeach
                    <span class="inline-flex items-center gap-1.5 text-xs text-gray-500">
                        <span class="w-6 h-6 rounded-md flex items-center justify-center font-bold text-[11px] bg-pink-100 text-pink-900">L</span>
                        On Leave
                    </span>
                    @foreach ($leaveTypeLabels as $type => $label)
                        <span class="inline-flex items-center gap-1.5 text-xs text-gray-500">
                            <span class="w-6 h-6 rounded-md flex items-center justify-center font-bold text-[9px] bg-pink-100 text-pink-900 truncate px-0.5" title="{{ $label }}">{{ $label }}</span>
                        </span>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="card p-10 text-center text-gray-400">
                <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                No published rosters available yet.
            </div>
        @endforelse
    </div>
</body>
</html>
