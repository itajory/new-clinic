<?php

namespace App\Livewire\UserManagement;

use App\Models\Role;
use App\Models\User;
use Mary\Traits\Toast;
use Livewire\Component;
use App\Models\Treatment;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\MedicalCenter;
use App\Livewire\Forms\UserForm;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

class UsersIndex extends Component
{
    use WithPagination, Toast;

    public UserForm $form;
    public array $headers;
    public string $searchWord = '';
    public bool $addModal = false;
    public bool $editMode = false;
    public bool $showFilterDrawer = false;

    public int $perPage;
    public array $perPageOptions;
    public array $sortBy;
    private $users;
    public Collection $roles;
    public Collection $medicalCenters;
    public Collection $treatments;
    public string $successMessage = '';
    private string $archivedMessage = '';
    public int $roleId = 0;
    public int $medicalCenterId = 0;
    public bool $showArchived = false;
    public bool $showChangePasswordModal = false;

    /// for confirmation modal
    public bool $showConfirmModal = false;

    public $confirmMessage = '';
    public bool $isDelete = false;
    public $confirmItemId = 0;

    public function save(): void
    {
        if ($this->editMode) {
            $this->authorize('update', $this->form->user);
            $this->form->update();
            $this->hideAddModal();
        } elseif ($this->showChangePasswordModal) {
            $this->authorize('update', $this->form->user);
            $this->form->changePassword();
            $this->hideChanhgePassword();
        } else {
            $this->authorize('create', User::class);
            $this->form->store();
            $this->hideAddModal();
        }
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
        $this->authorize('viewAny', User::class);
        $this->headers = [
            ['key' => 'id', 'label' => __('id')],
            ['key' => 'name', 'label' => __('name')],
            ['key' => 'email', 'label' => __('email')],
            ['key' => 'username', 'label' => __('username')],
            ['key' => 'role.name', 'label' => __('role'), 'sortable' => false],
        ];
        $this->perPageOptions = [10, 20, 50, 100];
        $this->perPage = $this->perPageOptions[0];
        $this->sortBy = ['column' => 'id', 'direction' => 'asc', 'class' => 'text-red-500'];
        $this->roles = Role::all();
        // $this->medicalCenters = MedicalCenter::all();
        $this->medicalCenters = Cache::remember('medical_centers', 60 * 60, function () {
            return MedicalCenter::all();
        });
        $this->treatments = Treatment::all();
        $this->successMessage = __('success');
        $this->archivedMessage = __('archived');
    }


    public function users()
    {
        return User::where(function ($query) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->searchWord) . '%'])
                ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($this->searchWord) . '%'])
                ->orWhereRaw('LOWER(username) LIKE ?', ['%' . strtolower($this->searchWord) . '%'])
                ->orWhere('id', 'like', '%' . $this->searchWord . '%');
        })
            ->when($this->roleId > 0, function ($query) {
                $query->whereHas('role', function ($query) {
                    $query->where('id', $this->roleId);
                });
            })
            ->when($this->medicalCenterId > 0, function ($query) {
                $query->whereHas('medicalCenters', function ($query) {
                    $query->where('id', $this->medicalCenterId);
                });
            })
            ->when($this->showArchived, function ($query) {
                $query->onlyTrashed();
            })
            ->orderBy(...array_values($this->sortBy))
            ->paginate($this->perPage);
    }

    public function showAddModal(): void
    {
        $this->addModal = true;
    }

    public function edit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->form->setUser($user);
        $this->editMode = true;
        $this->addModal = true;
    }

    public function changePassword(int $id): void
    {
        $user = User::findOrFail($id);
        $this->form->setUser($user);
        $this->showChangePasswordModal = true;
    }

    public function hideAddModal(): void
    {
        $this->addModal = false;
        $this->editMode = false;
        $this->form->reset();
        $this->resetErrorBag();
    }

    public function hideChanhgePassword()
    {
        $this->showChangePasswordModal = false;
        $this->form->reset();
        $this->resetErrorBag();
    }

    #[On('confiirmDelete')]
    public function delete(int $id = 0): void
    {
        $user = User::findOrFail($this->confirmItemId);
        $this->authorize('delete', $user);
        $user->delete();
        $this->warning(
            $this->archivedMessage,
            position: 'bottom-end',
            icon: 'c-trash',
            css: 'bg-warning text-white'
        );
        $this->resetConfirmModal();
    }

    #[On('confirmResetore')]
    public function restore($id = 0)
    {
        $user = User::withTrashed()->findOrFail($this->confirmItemId);
        $this->authorize('restore', $user);
        $user->restore();
        $this->resetConfirmModal();
    }




    public function getRowDecoration(): array
    {
        return [
            'hover:!bg-accent' => fn($role) => true
        ];
    }


    public function disableInputsWhenEditMode(): bool
    {
        return $this->editMode && !Gate::allows('update', $this->form->user);
    }

    public function filtersCount(): int
    {
        $filters = [
            $this->searchWord !== '',
            $this->roleId > 0,
            $this->medicalCenterId > 0,
            $this->showArchived === true
        ];
        return count(array_filter($filters));
    }

    public function clearFilters(): void
    {
        $this->roleId = 0;
        $this->medicalCenterId = 0;
        $this->searchWord = '';
        $this->showArchived = false;
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
    public function render()
    {
        return view('livewire.user-management.users-index')
            ->layout('layouts.dash');
    }
}
