<x-filament-panels::page>
    @php
        $user      = auth()->user();
        $hour      = now()->hour;
        $greeting  = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
        $roleLabel = match ($user->role) {
            'in_charge'    => 'Nurse In-Charge' . ($user->unit ? ' · ' . $user->unit->name : ''),
            'admin'        => 'System Administrator',
            'area_manager' => 'Area Manager',
            'head_nursing' => 'Head of Nursing',
            default        => ucfirst(str_replace('_', ' ', $user->role)),
        };
    @endphp

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
        <div>
            <p style="font-size:1.05rem;font-weight:700;margin:0;">{{ $greeting }}, {{ explode(' ', $user->name)[0] }}</p>
            <p style="font-size:0.8rem;color:var(--gray-500,#6b7280);margin:0.1rem 0 0;">{{ $roleLabel }}</p>
        </div>
        <p style="font-size:0.8rem;color:var(--gray-400,#9ca3af);margin:0;">{{ now()->format('l, d F Y') }}</p>
    </div>

    <x-filament-widgets::widgets
        :widgets="$this->getWidgets()"
        :columns="$this->getColumns()"
    />

</x-filament-panels::page>
