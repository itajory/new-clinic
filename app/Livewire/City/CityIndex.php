<?php

namespace App\Livewire\City;

use App\Models\City;
use Mary\Traits\Toast;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Forms\CityForm;
use Illuminate\Database\Eloquent\Collection;

class CityIndex extends Component
{
    use Toast;

    public CityForm $form;
    public array $headers;
    public string $searchWord = '';
    public bool $addModal = false;
    public bool $editMode = false;
    public Collection $cities;
    public bool $showArchived = false;
    private string $archivedMessage = '';
    public string $successMessage = '';

    /// for confirmation modal
    public bool $showConfirmModal = false;

    public $confirmMessage = '';
    public bool $isDelete = false;
    public $confirmItemId = 0;


    public function save(): void
    {
        if ($this->editMode) {
            $this->authorize('update', $this->form->city);
            $this->form->update();
        } else {
            $this->authorize('create', City::class);
            $this->form->store();
        }
        $this->hideModal();
        $this->success(
            "<u>{$this->successMessage}</u>",
            position: 'bottom-end',
            icon: 'c-check',
            css: 'bg-primary text-white'
        );
    }

    public function mount()
    {
        $this->authorize('viewAny', City::class);
        $this->headers = [
            ['key' => 'id', 'label' => __('id')],
            ['key' => 'name', 'label' => __('name')],
        ];
        $this->successMessage = __('success');
        $this->archivedMessage = __('archived');
    }

    public function search(): void
    {
        $this->cities = City::where(function ($query) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->searchWord) . '%'])
                ->orWhere('id', 'like', "%{$this->searchWord}%");
        })
            ->when($this->showArchived, function ($query) {
                $query->onlyTrashed();
            })
            ->get();
    }

    public function render()
    {
        $this->search();
        return view('livewire.city.city-index')
            ->layout('layouts.dash');
    }


    public function showAddModal(): void
    {
        $this->addModal = true;
    }

    public function edit(int $id): void
    {
        $city = City::findOrFail($id);
        $this->form->setCity($city);
        $this->editMode = true;
        $this->addModal = true;
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
        $city = City::findOrFail($this->confirmItemId);
        $this->authorize('delete', $city);
        $city->delete();
        $this->warning(
            $this->archivedMessage,
            position: 'bottom-end',
            icon: 'c-trash',
            css: 'bg-warning text-white'
        );
        $this->resetConfirmModal();
    }

    #[On('confirmResetore')]
    public function restore(int $id = 0)
    {
        $city = City::withTrashed()->findOrFail($this->confirmItemId);
        $this->authorize('restore', $city);
        $city->restore();
        $this->resetConfirmModal();
    }

    public function forceDelete(int $id): void
    {
        $city = City::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $city);
        $city->forceDelete();
    }

    public function confirmDelete(int $id): void
    {
        $this->dispatchBrowserEvent('confirm-delete', [
            'model' => 'city',
            'id' => $id,
        ]);
    }

    public function confirmRestore(int $id): void
    {
        $this->dispatchBrowserEvent('confirm-restore', [
            'model' => 'city',
            'id' => $id,
        ]);
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
