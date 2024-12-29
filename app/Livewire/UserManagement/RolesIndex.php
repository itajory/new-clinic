<?php

namespace App\Livewire\UserManagement;

use App\Models\Role;
use http\Env\Request;
use Mary\Traits\Toast;
use Livewire\Component;
use App\Models\Permission;
use Livewire\Attributes\On;
use App\Livewire\Forms\RoleForm;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class RolesIndex extends Component
{
    use Toast;

    public RoleForm $form;
    public array $headers;
    public Collection $roles;
    public array $permissions;
    public bool $addModal = false;
    public bool $editMode = false;
    public bool $showArchived = false;

    public $successMessage;

    /// for confirmation modal
    public bool $showConfirmModal = false;

    public $confirmMessage = '';
    public bool $isDelete = false;
    public $confirmItemId = 0;

    //    public array $selectedPermissions= [];


    public function save(): void
    {
        if ($this->editMode) {
            $this->authorize('update', $this->form->role);
            $this->form->update();
        } else {
            $this->authorize('create', Role::class);
            $this->form->store();
        }
        $this->hideModal();
        $this->success(
            "<u>{$this->successMessage}</u>",
            //            'You will <strong>love it :)</strong>',
            position: 'bottom-end',
            icon: 'c-check',
            css: 'bg-primary text-white'
        );
    }


    public function mount()
    {
        $this->headers = [
            ['key' => 'id', 'label' => __('id')],
            ['key' => 'name', 'label' => __('name')],
        ];
        $this->successMessage = __('success');
        $this->permissions = Permission::all()->groupBy('table_name')->toArray();
    }


    public function render()
    {
        //        $this->roles = Role::all(); // update the roles every time the component is rendered
        return view('livewire.user-management.roles-index')->layout('layouts.dash');
    }

    public function roles()
    {
        return Role::when(
            $this->showArchived,
            fn($query) => $query->onlyTrashed()
        )->get();
    }

    public function showAddModal(): void
    {
        $this->addModal = true;
    }

    public function edit(int $id): void
    {
        $role = Role::findOrFail($id);
        $this->form->setRole($role);
        //        $this->selectedPermissions = $this->form->role->permissions->pluck('id')->toArray();
        $this->addModal = true;
        $this->editMode = true;
    }

    public function hideModal(): void
    {
        $this->addModal = false;
        $this->editMode = false;
        $this->form->reset();
        $this->resetErrorBag();
    }

    #[On('confiirmDelete')]
    public function delete(int $id = 0): void
    {
        $role = Role::findOrFail($this->confirmItemId);
        $this->authorize('delete', $role);
        $role->delete();
        $this->resetConfirmModal();
    }

    #[On('confirmResetore')]
    public function restore(int $id = 0)
    {
        $role = Role::withTrashed()->findOrFail($this->confirmItemId);
        $this->authorize('restore', $role);
        $role->restore();
        $this->resetConfirmModal();
    }

    public function getRowDecoration(): array
    {
        return [
            'hover:!bg-accent' => fn($role) => true
        ];
    }

    public function changeShowConfirmModal($showModal, $message, $isDelete = false, $confirmItemId = 0,): void
    {
        $this->showConfirmModal = $showModal;
        $this->confirmMessage = $message;
        $this->isDelete = $isDelete;
        $this->confirmItemId = $confirmItemId;
    }

    #[On('confeirmReset')]
    public function resetConfirmModal(): void
    {
        $this->showConfirmModal = false;
        $this->confirmMessage = '';
        $this->confirmItemId = 0;
        $this->isDelete = false;
    }
}
