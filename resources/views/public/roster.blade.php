<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($unit) ? $unit->name . ' — Roster' : 'Staff Roster' }}</title>
    @fonts
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-gray-100">

    {{-- Top bar --}}
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="{{ asset('hospital-logo.svg') }}" alt="MSWNH Logo" class="w-20 h-20 shrink-0 object-contain">
            <div>
                <p class="text-sm font-bold text-gray-900 leading-tight">Mulago Specialized Women and Neonatal Hospital</p>
                <p class="text-xs text-gray-400 leading-tight">Staff Roster Management System</p>
            </div>
        </div>
        <a href="{{ route('filament.admin.auth.login') }}" class="text-xs text-gray-400 hover:text-gray-600 transition-colors">
            Staff login →
        </a>
    </header>

    <div class="max-w-5xl mx-auto px-4 py-8">

        {{-- Unit picker --}}
        <div class="mb-8">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Select a Unit</label>
            <div class="flex gap-3">
                <select id="unit-select"
                        class="flex-1 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-[--color-primary-500]">
                    <option value="">— Choose a unit —</option>
                    @foreach ($units as $u)
                        <option value="{{ route('public.roster.unit', $u->id) }}" {{ isset($unit) && $unit->id === $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <script>
            document.getElementById('unit-select').addEventListener('change', function(e) {
                if (e.target.value) {
                    window.location.href = e.target.value;
                }
            });
        </script>

        @if (!isset($unit))
            {{-- Landing state --}}
            <div class="card p-12 text-center text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <p class="text-sm">Choose a unit above to view the published roster.</p>
            </div>

        @elseif ($periodsData->isEmpty())
            <div class="card p-12 text-center text-gray-400">
                <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm">No published rosters for <strong>{{ $unit->name }}</strong> yet.</p>
            </div>

        @else
            @php
                $pillColors = [
                    'D' => 'bg-blue-100 text-blue-800',
                    'E' => 'bg-amber-100 text-amber-800',
                    'N' => 'bg-indigo-100 text-indigo-800',
                    'O' => 'bg-gray-100 text-gray-500',
                ];
                $codeLabels = ['D' => 'Day', 'E' => 'Evening', 'N' => 'Night', 'O' => 'Off'];
            @endphp

            <h2 class="text-sm font-semibold text-gray-700 mb-4">{{ $unit->name }} — Published Rosters</h2>

            @foreach ($periodsData as $item)
                @php
                    $period  = $item['period'];
                    $dates   = $item['dates'];
                    $nurses  = $item['nurses'];
                    $byNurse = $item['byNurse'];
                @endphp

                <div class="card mb-6 overflow-hidden">
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
                                    <th class="px-3 py-2 text-left font-medium text-gray-500 border-r border-gray-100 min-w-[140px] sticky left-0 bg-gray-50">
                                        Nurse
                                    </th>
                                    @foreach ($dates as $date)
                                        <th class="px-1 py-2 text-center font-medium text-gray-500 border-r border-gray-100 last:border-r-0 min-w-[40px] {{ $date->isWeekend() ? 'bg-blue-50' : '' }}">
                                            <div class="text-[10px] text-gray-400">{{ $date->format('D') }}</div>
                                            <div class="text-gray-800 font-bold text-[13px] leading-tight">{{ $date->format('d') }}</div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($nurses as $nurse)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-3 py-2 font-medium text-gray-700 border-r border-gray-100 sticky left-0 bg-white">
                                            {{ $nurse->name }}
                                            @if ($nurse->grade)
                                                <span class="text-gray-400 font-normal">· {{ $nurse->grade }}</span>
                                            @endif
                                        </td>
                                        @foreach ($dates as $date)
                                            @php $shift = $byNurse[$nurse->id][$date->toDateString()] ?? null; @endphp
                                            <td class="px-1 py-2 text-center border-r border-gray-100 last:border-r-0 {{ $date->isWeekend() ? 'bg-blue-50/30' : '' }}">
                                                @if ($shift)
                                                    <span class="inline-flex w-7 h-7 rounded-md items-center justify-center font-bold text-xs {{ $pillColors[$shift->code] ?? 'bg-gray-100 text-gray-500' }}"
                                                          title="{{ $codeLabels[$shift->code] ?? $shift->code }}">
                                                        {{ $shift->code }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-200">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
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
                    </div>
                </div>
            @endforeach
        @endif
    </div>

</body>
</html>
