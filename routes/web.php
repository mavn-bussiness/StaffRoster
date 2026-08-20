<?php

use App\Http\Controllers\NurseRosterController;
use App\Http\Controllers\PublicRosterController;
use App\Http\Controllers\RosterOverviewController;
use Illuminate\Support\Facades\Route;

// --- Public roster (default landing page, no login required) ---
Route::get('/', [PublicRosterController::class, 'index'])->name('public.roster');
Route::get('/roster', fn () => redirect()->route('public.roster'));
Route::get('/roster/unit/{unit}', [PublicRosterController::class, 'unit'])->name('public.roster.unit');

// --- Individual nurse roster view (no login required) ---
Route::get('/nurse/{nurse}/roster', [NurseRosterController::class, 'show'])->name('nurse.roster');

// --- Print view (opened from Filament admin) ---
Route::get('/rosters/{rosterPeriod}/print', [RosterOverviewController::class, 'print'])
    ->name('rosters.print')
    ->middleware('auth');
