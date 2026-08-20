<?php

namespace App\Livewire\Admin;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class ManageUsers extends Component
{
    public bool $showModal = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $role = User::ROLE_IN_CHARGE;

    public ?int $unit_id = null;

    // Roles that require a unit assignment
    private const UNIT_SCOPED = [User::ROLE_IN_CHARGE, User::ROLE_AREA_MANAGER];

    public function openCreate(): void
    {
        $this->reset(['editingId', 'name', 'email', 'password', 'unit_id']);
        $this->role = User::ROLE_IN_CHARGE;
        $this->showModal = true;
    }

    public function openEdit(User $user): void
    {
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->role = $user->role;
        $this->unit_id = $user->unit_id;
        $this->showModal = true;
    }

    public function save(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->editingId)],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_IN_CHARGE, User::ROLE_AREA_MANAGER, User::ROLE_HEAD_NURSING])],
            'unit_id' => in_array($this->role, self::UNIT_SCOPED) ? ['required', 'exists:units,id'] : ['nullable'],
            'password' => $this->editingId ? ['nullable', 'string', 'min:8'] : ['required', 'string', 'min:8'],
        ];

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'unit_id' => in_array($this->role, self::UNIT_SCOPED) ? $this->unit_id : null,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editingId) {
            User::findOrFail($this->editingId)->update($data);
            session()->flash('status', "Account for {$this->name} updated.");
        } else {
            User::create($data);
            session()->flash('status', "Account created for {$this->name}.");
        }

        $this->showModal = false;
        $this->reset(['editingId', 'name', 'email', 'password', 'unit_id']);
    }

    public function delete(User $user): void
    {
        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');

            return;
        }

        $user->delete();
        session()->flash('status', 'Account deleted.');
    }

    public function render(): View
    {
        return view('livewire.admin.manage-users', [
            'users' => User::with('unit')->orderBy('role')->orderBy('name')->get(),
            'units' => Unit::orderBy('name')->get(),
            'roles' => [
                User::ROLE_ADMIN => 'Admin',
                User::ROLE_IN_CHARGE => 'In-Charge',
                User::ROLE_AREA_MANAGER => 'Area Manager',
                User::ROLE_HEAD_NURSING => 'Head Nursing',
            ],
        ])->layout('layouts.app', ['title' => 'User Accounts']);
    }
}
