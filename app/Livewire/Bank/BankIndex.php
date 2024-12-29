<?php

namespace App\Livewire\Bank;

use App\Models\Bank;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Forms\BankForm;
use Illuminate\Database\Eloquent\Collection;

class BankIndex extends Component
{
    public BankForm $form;
    public array $headers;
    public string $searchWord = '';
    public bool $addModal = false;
    public bool $editMode = false;
    public bool $showArchived = false;
    public Collection $banks;

    /// for confirmation modal
    public bool $showConfirmModal = false;

    public $confirmMessage = '';
    public bool $isDelete = false;
    public $confirmItemId = 0;

    public function save(): void
    {
        if ($this->editMode) {
            $this->authorize('update', $this->form->bank);
            $this->form->update();
        } else {
            $this->authorize('create', Bank::class);
            $this->form->store();
        }
        $this->hideModal();
    }

    public function mount()
    {
        $this->authorize('viewAny', Bank::class);
        $this->headers = [
            ['key' => 'id', 'label' => __('id')],
            ['key' => 'name', 'label' => __('name')],
            ['key' => 'number', 'label' => __('number')],
        ];
    }

    public function search(): void
    {
        $this->banks = Bank::where(function ($query) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->searchWord) . '%'])
                ->orWhereRaw('LOWER(number) LIKE ?', ['%' . strtolower($this->searchWord) . '%'])
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

        return view('livewire.bank.bank-index')->layout('layouts.dash');
    }

    public function showAddModal(): void
    {
        $this->addModal = true;
    }

    public function edit(int $id): void
    {
        $bank = Bank::findOrFail($id);
        $this->form->setBank($bank);
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
        $bank = Bank::findOrFail($this->confirmItemId);
        $this->authorize('delete', $bank);
        $bank->delete();
        $this->resetConfirmModal();
    }
    #[On('confirmResetore')]
    public function restore(int $id = 0): void
    {
        $bank = Bank::withTrashed()->findOrFail($this->confirmItemId);
        $this->authorize('restore', $bank);
        $bank->restore();
        $this->resetConfirmModal();
    }

    public function forceDelete(int $id): void
    {
        $bank = Bank::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $bank);
        $bank->forceDelete();
    }

    public function confirmDelete(int $id): void
    {
        $this->dispatchBrowserEvent('confirm-delete', ['model' => 'bank', 'id' => $id]);
    }

    public function confirmRestore(int $id): void
    {
        $this->dispatchBrowserEvent('confirm-restore', ['model' => 'bank', 'id' => $id]);
    }

    public function getRowDecoration(): array
    {
        return [
            'hover:!bg-accent' => fn($role) => true,
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
