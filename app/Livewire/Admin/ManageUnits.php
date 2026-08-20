<?php

namespace App\Livewire\Admin;

use App\Models\Unit;
use Illuminate\View\View;
use Livewire\Attributes\Rule;
use Livewire\Component;

class ManageUnits extends Component
{
    public bool $showModal = false;

    public ?int $editingId = null;

    #[Rule(['required', 'string', 'max:255'])]
    public string $name = '';

    #[Rule(['required', 'string', 'max:20'])]
    public string $code = '';

    public bool $has_evening_shift = true;

    public bool $has_night_shift = true;

    public function openCreate(): void
    {
        $this->reset(['editingId', 'name', 'code', 'has_evening_shift', 'has_night_shift']);
        $this->has_evening_shift = true;
        $this->has_night_shift = true;
        $this->showModal = true;
    }

    public function openEdit(Unit $unit): void
    {
        $this->editingId = $unit->id;
        $this->name = $unit->name;
        $this->code = $unit->code;
        $this->has_evening_shift = $unit->has_evening_shift;
        $this->has_night_shift = $unit->has_night_shift;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required', 'string', 'max:20',
                \Illuminate\Validation\Rule::unique('units', 'code')->ignore($this->editingId),
            ],
        ]);

        $data = [
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'has_evening_shift' => $this->has_evening_shift,
            'has_night_shift' => $this->has_night_shift,
        ];

        if ($this->editingId) {
            Unit::findOrFail($this->editingId)->update($data);
            session()->flash('status', "Unit \"{$this->name}\" updated.");
        } else {
            Unit::create($data);
            session()->flash('status', "Unit \"{$this->name}\" created.");
        }

        $this->showModal = false;
        $this->reset(['editingId', 'name', 'code']);
    }

    public function delete(Unit $unit): void
    {
        if ($unit->nurses()->exists() || $unit->users()->exists()) {
            session()->flash('error', 'Cannot delete a unit that still has nurses or accounts assigned.');

            return;
        }

        $unit->delete();
        session()->flash('status', 'Unit deleted.');
    }

    public function render(): View
    {
        return view('livewire.admin.manage-units', [
            'units' => Unit::withCount(['nurses', 'users'])->orderBy('name')->get(),
        ])->layout('layouts.app', ['title' => 'Units']);
    }
}
