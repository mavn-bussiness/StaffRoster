<?php

namespace App\Filament\Pages;

use App\Models\Shift;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use UnitEnum;

class OnDuty extends Page
{
    protected string $view = 'filament.pages.on-duty';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clock';

    protected static UnitEnum|string|null $navigationGroup = 'Overview';

    protected static ?string $navigationLabel = 'On Duty Today';

    protected static ?string $title = 'On Duty Today';

    #[Url]
    public ?string $date = null;

    public function boot(): void
    {
        $this->date ??= Carbon::today()->toDateString();
    }

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['admin', 'area_manager', 'head_nursing']);
    }

    public function getOnDutyByUnit(): Collection
    {
        $user = auth()->user();

        $query = Shift::with(['nurse.currentUnit'])
            ->whereDate('shift_date', Carbon::parse($this->date))
            ->where('code', '!=', 'O')
            ->whereHas('rosterPeriod', fn ($q) => $q->whereIn('status', ['agreed', 'notified', 'submitted']));

        if ($user?->role === 'area_manager' && $user->unit_id) {
            $query->whereHas('nurse', fn ($q) => $q->where('current_unit_id', $user->unit_id));
        }

        return $query->get()
            ->groupBy(fn ($shift) => $shift->nurse->currentUnit->name ?? 'Unassigned');
    }

    public function getDate(): Carbon
    {
        return Carbon::parse($this->date ?: Carbon::today()->toDateString());
    }
}
