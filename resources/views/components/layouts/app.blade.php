{{-- This file intentionally mirrors layouts/app.blade.php --}}
{{-- Both <x-layouts.app> (blade views) and ->layout('layouts.app') (Livewire) --}}
{{-- must render identically. Keep these two files in sync. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }} — Staff Roster</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 text-gray-900 antialiased">

@auth
@php $role = auth()->user()->role; $user = auth()->user(); @endphp

<div class="flex h-screen overflow-hidden">

    {{-- ── Sidebar ──────────────────────────────────────────── --}}
    <aside id="sidebar" class="w-64 shrink-0 flex flex-col bg-[--color-sidebar-bg] overflow-y-auto transition-all duration-300" x-data="{ open: true }" :class="open ? 'w-64' : 'w-20'">

        {{-- Brand --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-white/5">
            <img src="{{ asset('hospital-logo.svg') }}" alt="MSWNH Logo" class="w-10 h-10 shrink-0 rounded-lg object-contain bg-white p-0.5">
            <div class="min-w-0 sidebar-text" x-show="open" x-transition>
                <p class="text-white font-semibold text-[11px] leading-tight">Mulago Specialized</p>
                <p class="text-white font-semibold text-[11px] leading-tight">Women & Neonatal Hospital</p>
                <p class="text-gray-500 text-[10px] leading-tight mt-0.5">Staff Roster System</p>
            </div>
        </div>

        {{-- Collapse toggle --}}
        <button @click="open = !open" class="absolute top-5 -right-3 z-10 w-6 h-6 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center text-gray-500 hover:text-gray-700">
            <svg x-show="open" x-cloak class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            <svg x-show="!open" x-cloak class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </button>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5">

            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span x-show="open" x-transition>Dashboard</span>
            </a>

            @if ($role === 'in_charge')
                <p class="sidebar-section" x-show="open" x-transition>Roster</p>
                <a href="{{ route('incharge.roster-periods.index') }}"
                   class="sidebar-link {{ request()->routeIs('incharge.roster-periods.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span x-show="open" x-transition>Roster Periods</span>
                </a>

                <p class="sidebar-section" x-show="open" x-transition>Unit</p>
                <a href="{{ route('filament.admin.resources.nurses.index') }}"
                   class="sidebar-link {{ request()->routeIs('filament.admin.resources.nurses.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span x-show="open" x-transition>Nurses</span>
                </a>
                <a href="{{ route('incharge.off-day-requests.index') }}"
                   class="sidebar-link {{ request()->routeIs('incharge.off-day-requests.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                    <span x-show="open" x-transition>Off-Day Requests</span>
                </a>
                <a href="{{ route('incharge.leave-requests.index') }}"
                   class="sidebar-link {{ request()->routeIs('incharge.leave-requests.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                    </svg>
                    <span x-show="open" x-transition>Leave Requests</span>
                </a>
                <a href="{{ route('incharge.transfers.create') }}"
                   class="sidebar-link {{ request()->routeIs('incharge.transfers.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    <span x-show="open" x-transition>Transfer Nurse</span>
                </a>
            @endif

            @if (in_array($role, ['admin', 'area_manager', 'head_nursing']))
                <p class="sidebar-section" x-show="open" x-transition>Overview</p>
                <a href="{{ route('rosters.index') }}"
                   class="sidebar-link {{ request()->routeIs('rosters.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span x-show="open" x-transition>All Rosters</span>
                </a>
                <a href="{{ route('on-duty.index') }}"
                   class="sidebar-link {{ request()->routeIs('on-duty.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728M12 8v4l3 3"/>
                    </svg>
                    <span x-show="open" x-transition>On Duty Today</span>
                </a>
            @endif

            @if ($role === 'admin')
                <p class="sidebar-section" x-show="open" x-transition>Administration</p>
                <a href="{{ route('filament.admin.resources.units.index') }}"
                   class="sidebar-link {{ request()->routeIs('filament.admin.resources.units.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span x-show="open" x-transition>Units</span>
                </a>
                <a href="{{ route('filament.admin.resources.users.index') }}"
                   class="sidebar-link {{ request()->routeIs('filament.admin.resources.users.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span x-show="open" x-transition>User Accounts</span>
                </a>
            @endif
        </nav>

        {{-- User footer --}}
        <div class="px-3 py-4 border-t border-white/5">
            <div class="flex items-center gap-3 px-3 py-2">
                <div class="w-8 h-8 rounded-full bg-[--color-primary-600] flex items-center justify-center text-white text-xs font-semibold shrink-0">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0 sidebar-text" x-show="open" x-transition>
                    <p class="text-sm font-medium text-white truncate">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500 truncate capitalize">{{ str_replace('_', ' ', $role) }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="sidebar-text" x-show="open" x-transition>
                    @csrf
                    <button type="submit" title="Sign out" class="text-gray-500 hover:text-red-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ── Main content ──────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Top bar --}}
        <header class="bg-white border-b border-gray-200 px-6 py-3.5 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <img src="{{ asset('hospital-logo.svg') }}" alt="MSWNH Logo" class="w-12 h-12 shrink-0 object-contain">
                <h1 class="text-sm font-semibold text-gray-800">{{ $title ?? config('app.name') }}</h1>
            </div>
            @if ($role === 'in_charge' && $user->unit)
                <span class="badge badge-blue">{{ $user->unit->name }}</span>
            @endif
        </header>

        {{-- Flash messages --}}
        @if (session('status'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="mx-6 mt-4" x-transition>
                <div class="alert-success">
                    <svg class="w-4 h-4 shrink-0 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="flex-1">{{ session('status') }}</span>
                    <button @click="show = false" class="text-green-600 hover:text-green-800">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="mx-6 mt-4">
                <div class="alert-error">
                    <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="flex-1">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Page slot --}}
        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </main>
    </div>
</div>

@else
    {{ $slot }}
@endauth

@livewireScripts
</body>
</html>
