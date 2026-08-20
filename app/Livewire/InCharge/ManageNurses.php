<?php

namespace App\Livewire\InCharge;

use App\Models\Nurse;
use App\Models\Unit;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ManageNurses extends Component
{
    use WithPagination;

    public bool $showModal = false;

    public ?int $editingId = null;

    public string $search = '';

    public string $name = '';

    public string $grade = '';

    public string $employment_type = '';

    public string $date_joined = '';

    public string $status = 'active';

    public function openCreate(): void
    {
        $this->reset(['editingId', 'name', 'grade', 'employment_type', 'date_joined']);
        $this->status = 'active';
        $this->showModal = true;
    }

    public function openEdit(Nurse $nurse): void
    {
        $this->editingId = $nurse->id;
        $this->name = $nurse->name;
        $this->grade = $nurse->grade ?? '';
        $this->employment_type = $nurse->employment_type ?? '';
        $this->date_joined = $nurse->date_joined?->format('Y-m-d') ?? '';
        $this->status = $nurse->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $unitId = auth()->user()->unit_id;

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'grade' => ['nullable', 'string', 'max:100'],
            'employment_type' => ['nullable', 'string', 'max:100'],
            'date_joined' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['active', 'on_leave', 'inactive'])],
        ]);

        $data = [
            'name' => $this->name,
            'grade' => $this->grade ?: null,
            'employment_type' => $this->employment_type ?: null,
            'date_joined' => $this->date_joined ?: null,
            'status' => $this->status,
        ];

        if ($this->editingId) {
            Nurse::findOrFail($this->editingId)->update($data);
            session()->flash('status', "{$this->name} updated.");
        } else {
            Nurse::create($data + [
                'home_unit_id' => $unitId,
                'current_unit_id' => $unitId,
            ]);
            session()->flash('status', "{$this->name} added to the unit.");
        }

        $this->showModal = false;
        $this->reset(['editingId', 'name', 'grade', 'employment_type', 'date_joined']);
    }

    public function delete(Nurse $nurse): void
    {
        $nurse->delete();
        session()->flash('status', 'Nurse record removed.');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $unitId = auth()->user()->unit_id;

        $nurses = Nurse::where('current_unit_id', $unitId)
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.incharge.manage-nurses', [
            'nurses' => $nurses,
            'unit' => Unit::find($unitId),
            'statusOptions' => ['active' => 'Active', 'on_leave' => 'On Leave', 'inactive' => 'Inactive'],
            'employmentTypes' => ['Permanent', 'Contract', 'Locum', 'Student'],
        ])->layout('layouts.app', ['title' => 'Nurses']);
    }
}
