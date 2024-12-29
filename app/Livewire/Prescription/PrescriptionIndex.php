<?php

namespace App\Livewire\Prescription;

use Livewire\Component;
use App\Models\Treatment;
use Livewire\Attributes\On;
use App\Models\PrescriptionTemplate;
use Illuminate\Support\Facades\Cache;
use App\Livewire\Forms\PrescriptionForm;
use Illuminate\Database\Eloquent\Collection;

class PrescriptionIndex extends Component
{
    public PrescriptionForm $form;
    public array $headers;
    public string $searchWord = '';
    public int $filterByTreatment = 0;
    public bool $addModal = false;
    public bool $editMode = false;
    public bool $showFilterDrawer = false;
    public bool $showArchived = false;
    //    public Collection $prescriptions;
    public Collection $treatments;
    /// for confirmation modal
    public bool $showConfirmModal = false;

    public $confirmMessage = '';
    public bool $isDelete = false;
    public $confirmItemId = 0;

    public function save(): void
    {
        if ($this->editMode) {
            $this->authorize('update', $this->form->prescriptionTemplate);
            $this->form->update();
        } else {
            $this->authorize('create', PrescriptionTemplate::class);
            $this->form->store();
        }
        $this->prescriptions();
        $this->hideModal();
        $this->resetCache();
        $this->resetCache();
    }

    public function mount()
    {
        $this->authorize('viewAny', PrescriptionTemplate::class);
        $this->treatments = Treatment::all();
        $this->headers = [
            ['key' => 'id', 'label' => __('id')],
            ['key' => 'name', 'label' => __('name')],
            ['key' => 'content', 'label' => __('content')],
            ['key' => 'treatment.name', 'label' => __('treatment')],
        ];
        $this->prescriptions();
    }

    public function updatedShowFilterDrawer($value)
    {
        if (!$value) {
            $this->filterByTreatment = 0;
            $this->prescriptions(); // if drawer closed after filter cleared
        }
    }

    public function resetFilterByTreatment(): void
    {
        $this->filterByTreatment = 0;
    }

    public function searchWithFilter()
    {

        $this->prescriptions();
        $this->showFilterDrawer = false;
    }

    public function prescriptions()
    {
        return PrescriptionTemplate::where(function ($query) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->searchWord) . '%'])
                ->orWhereRaw('LOWER(content) LIKE ?', ['%' . strtolower($this->searchWord) . '%'])
                ->orWhere('id', 'like', '%' . $this->searchWord . '%');
        })->when($this->filterByTreatment > 0, function ($query) {
            $query->where('treatment_id', $this->filterByTreatment);
        })
            ->when($this->showArchived, function ($query) {
                $query->onlyTrashed();
            })->with('treatment')
            ->get();
    }

    public function render()
    {
        return view('livewire.prescription.prescription-index')->layout('layouts.dash');
    }

    public function showAddModal(): void
    {
        $this->addModal = true;
    }

    public function edit(int $id): void
    {
        $prescription = PrescriptionTemplate::findOrFail($id);
        $this->form->setPrescriptionTemplate($prescription);
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
        $prescription = PrescriptionTemplate::findOrFail($this->confirmItemId);
        $this->authorize('delete', $prescription);
        $prescription->delete();
        $this->resetCache();
        $this->resetConfirmModal();
    }
    #[On('confirmResetore')]
    public function restore(int $id = 0)
    {
        $prescription = PrescriptionTemplate::withTrashed()->findOrFail($this->confirmItemId);
        $this->authorize('restore', $prescription);
        $prescription->restore();
        $this->resetCache();
        $this->resetConfirmModal();
    }

    public function forceDelete(int $id): void
    {
        $prescription = PrescriptionTemplate::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $prescription);
        $prescription->forceDelete();
        $this->resetCache();
    }

    public function filtersCount(): int
    {
        $count = 0;
        if (!empty($this->searchWord)) {
            $count++;
        }
        if ($this->filterByTreatment > 0) {
            $count++;
        }
        if ($this->showArchived) {
            $count++;
        }
        return $count;
    }

    public function clearFilters(): void
    {
        $this->searchWord = '';
        $this->filterByTreatment = 0;
        $this->showArchived = false;
    }

    public function getRowDecoration(): array
    {
        return [
            'hover:!bg-accent' => fn($role) => true
        ];
    }

    public function resetCache()
    {
        Cache::forget('prescriptions');
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
