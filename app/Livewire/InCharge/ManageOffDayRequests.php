<?php

namespace App\Livewire\InCharge;

use App\Models\Nurse;
use App\Models\OffDayRequest;
use App\Models\RosterPeriod;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class ManageOffDayRequests extends Component
{
    public bool $showModal = false;

    public ?int $editingId = null;

    public ?int $nurse_id = null;

    public ?int $roster_period_id = null;

    public string $requested_date = '';

    public string $reason = '';

    public string $written_request_date = '';

    public string $status = 'requested';

    public function openCreate(): void
    {
        $this->reset(['editingId', 'nurse_id', 'roster_period_id', 'requested_date', 'reason', 'written_request_date']);
        $this->status = 'requested';
        $this->showModal = true;
    }

    public function openEdit(OffDayRequest $offDayRequest): void
    {
        $this->editingId = $offDayRequest->id;
        $this->nurse_id = $offDayRequest->nurse_id;
        $this->roster_period_id = $offDayRequest->roster_period_id;
        $this->requested_date = $offDayRequest->requested_date->format('Y-m-d');
        $this->reason = $offDayRequest->reason ?? '';
        $this->written_request_date = $offDayRequest->written_request_date?->format('Y-m-d') ?? '';
        $this->status = $offDayRequest->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $unitId = auth()->user()->unit_id;

        $this->validate([
            'nurse_id' => ['required', 'exists:nurses,id'],
            'roster_period_id' => ['required', 'exists:roster_periods,id'],
            'requested_date' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:255'],
            'written_request_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['requested', 'granted', 'denied'])],
        ]);

        $data = [
            'nurse_id' => $this->nurse_id,
            'roster_period_id' => $this->roster_period_id,
            'requested_date' => $this->requested_date,
            'reason' => $this->reason ?: null,
            'written_request_date' => $this->written_request_date ?: null,
            'status' => $this->status,
        ];

        if ($this->editingId) {
            OffDayRequest::findOrFail($this->editingId)->update($data);
            session()->flash('status', 'Off-day request updated.');
        } else {
            OffDayRequest::create($data);
            session()->flash('status', 'Off-day request logged.');
        }

        $this->showModal = false;
        $this->reset(['editingId', 'nurse_id', 'roster_period_id', 'requested_date', 'reason', 'written_request_date']);
    }

    public function delete(OffDayRequest $offDayRequest): void
    {
        $offDayRequest->delete();
        session()->flash('status', 'Off-day request removed.');
    }

    public function render(): View
    {
        $unitId = auth()->user()->unit_id;

        return view('livewire.incharge.manage-off-day-requests', [
            'requests' => OffDayRequest::whereHas('nurse', fn ($q) => $q->where('current_unit_id', $unitId))
                ->with('nurse', 'rosterPeriod')
                ->orderByDesc('requested_date')
                ->get(),
            'nurses' => Nurse::where('current_unit_id', $unitId)->orderBy('name')->get(),
            'rosterPeriods' => RosterPeriod::where('unit_id', $unitId)->whereIn('status', ['draft', 'agreed'])->orderByDesc('period_start')->get(),
            'statusOptions' => ['requested' => 'Requested', 'granted' => 'Granted', 'denied' => 'Denied'],
        ])->layout('layouts.app', ['title' => 'Off-Day Requests']);
    }
}
