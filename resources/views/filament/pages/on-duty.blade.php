<x-filament-panels::page>
    @php
        $onDutyByUnit = $this->getOnDutyByUnit();
        $date = $this->getDate();
        $shiftLabels = ['D' => 'Day', 'E' => 'Evening', 'N' => 'Night'];
        $shiftColors = ['D' => 'info', 'E' => 'warning', 'N' => 'primary'];
    @endphp

    <p class="text-sm text-gray-500 dark:text-gray-400 -mt-2 mb-4">{{ $date->format('l, d F Y') }}</p>

    @if ($onDutyByUnit->isEmpty())
        <x-filament::section>
            <div class="py-8 text-center text-gray-400 dark:text-gray-500">
                No shifts recorded for today across any unit.
            </div>
        </x-filament::section>
    @else
        <div class="space-y-4">
            @foreach ($onDutyByUnit as $unitName => $shifts)
                <x-filament::section :heading="$unitName">
                    <x-slot name="headerEnd">
                        <x-filament::badge color="info">{{ $shifts->count() }} on duty</x-filament::badge>
                    </x-slot>

                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Nurse</th>
                                <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Grade</th>
                                <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Shift</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($shifts->sortBy('nurse.name') as $shift)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                                    <td class="py-2.5 px-3 font-medium text-gray-900 dark:text-gray-100">{{ $shift->nurse->name }}</td>
                                    <td class="py-2.5 px-3 text-gray-500 dark:text-gray-400">{{ $shift->nurse->grade ?? '—' }}</td>
                                    <td class="py-2.5 px-3">
                                        <x-filament::badge :color="$shiftColors[$shift->code] ?? 'gray'">
                                            {{ $shiftLabels[$shift->code] ?? $shift->code }}
                                        </x-filament::badge>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </x-filament::section>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
