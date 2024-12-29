<?php

namespace App\Livewire\PatientFund;

use Livewire\Component;
use App\Models\PatientFund;
use Livewire\Attributes\On;
use App\Livewire\Forms\PaitientFundForm;
use Illuminate\Database\Eloquent\Collection;

class PatientFundIndex extends Component
{
    public PaitientFundForm $form;
    public array $headers;
    public string $searchWord = '';
    public bool $addModal = false;
    public bool $editMode = false;
    public bool $showArchived = false;
    public Collection $patientFunds;

    public array $contributionTypes = [
        'percentage',
        'fixed'
    ];
    /// for confirmation modal
    public bool $showConfirmModal = false;

    public $confirmMessage = '';
    public bool $isDelete = false;
    public $confirmItemId = 0;
    public function mount()
    {
        $this->authorize('viewAny', PatientFund::class);
        $this->headers = [
            ['key' => 'id', 'label' => __('id')],
            ['key' => 'name', 'label' => __('name')],
            ['key' => 'contribution_type', 'label' => __('contribution_type')],
        ];
    }

    public function search(): void
    {
        $this->patientFunds = PatientFund::where(function ($query) {
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
        return view('livewire.patient-fund.patient-fund-index')
            ->layout('layouts.dash');
    }

    public function showAddModal(): void
    {
        $this->addModal = true;
    }

    public function edit(int $id): void
    {
        $patientFund = PatientFund::findOrFail($id);
        $this->form->setPatientFund($patientFund);
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
        $patientFund = PatientFund::findOrFail($this->confirmItemId);
        $this->authorize('delete', $patientFund);
        $patientFund->delete();
        $this->resetConfirmModal();
    }

    #[On('confirmResetore')]
    public function restore(int $id = 0)
    {
        $patientFund = PatientFund::withTrashed()->findOrFail($this->confirmItemId);
        $this->authorize('restore', $patientFund);
        $patientFund->restore();
        $this->resetConfirmModal();
    }

    public function save(): void
    {
        if ($this->editMode) {
            $this->authorize('update', $this->form->patientFund);
            $this->form->update();
        } else {
            $this->authorize('create', PatientFund::class);
            $this->form->store();
        }
        $this->hideModal();
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
