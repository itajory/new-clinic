<?php

namespace App\Livewire\Patient;

use App\Models\City;
use App\Models\Role;
use App\Models\User;
use Mary\Traits\Toast;
use App\Models\Patient;
use Livewire\Component;
use App\Models\PatientFund;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\MedicalCenter;
use App\Livewire\Forms\PatientForm;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Collection;

class PatientIndex extends Component
{
    use WithPagination, Toast;

    public PatientForm $form;
    public array $headers;
    public bool $addModal = false;
    public bool $editMode = false;
    public bool $showArchived = false;
    public bool $showFilterDrawer = false;
    public string $successMessage = '';
    public string $archivedMessage = '';
    public string $searchWord = '';
    public string $searchPatientFundWord = '';
    public int $perPage;
    public array $perPageOptions;
    public array $sortBy;
    public Collection $cities;
    //    public Collection $patientFunds;
    public int $medicalCenterId = 0;
    public int $cityId = 0;

    /// for confirmation modal
    public bool $showConfirmModal = false;

    public $confirmMessage = '';
    public bool $isDelete = false;
    public $confirmItemId = 0;

    public function save(): void
    {
        if ($this->editMode) {
            $this->authorize('update', $this->form->patient);
            $this->form->update();
            $this->hideAddModal();
        } else {
            $this->authorize('create', Patient::class);
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
        $this->authorize('viewAny', Patient::class);
        $this->headers = [
            ['key' => 'id', 'label' => __('id')],
            ['key' => 'full_name', 'label' => __('full_name')],
            ['key' => 'gender', 'label' => __('gender')],
            ['key' => 'id_number', 'label' => __('id_number')],
            ['key' => 'birth_date', 'label' => __('birth_date')],
            ['key' => 'actions', 'label' => __('actions')],
        ];
        $this->perPageOptions = [10, 20, 50, 100];
        $this->perPage = $this->perPageOptions[0];
        $this->sortBy = ['column' => 'id', 'direction' => 'asc', 'class' => 'text-red-500'];
        $this->successMessage = __('success');
        $this->archivedMessage = __('archived');
        $this->cities = City::all();
        //        $this->patientFunds = PatientFund::all();
    }

    public function patientFunds()
    {
        return PatientFund::where(function ($query) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->searchPatientFundWord) . '%'])
                ->orWhere('id', 'like', '%' . $this->searchPatientFundWord . '%');
        })
            ->get();
    }

    public function patients()
    {
        return Patient::where(function ($query) {
            $query->whereRaw('LOWER(full_name) LIKE ?', ['%' . strtolower($this->searchWord) . '%'])
                ->orWhereRaw('LOWER(id_number) LIKE ?', ['%' . strtolower($this->searchWord) . '%'])
                ->orWhere('id', 'like', '%' . $this->searchWord . '%');
        })
            ->when($this->cityId > 0, function ($query) {
                $query->where('city_id', $this->cityId);
            })
            //            ->when($this->medicalCenterId > 0, function ($query) {
            //                $query->whereHas('medicalCenters', function ($query) {
            //                    $query->where('id', $this->medicalCenterId);
            //                });
            //            })
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

    public function edit(int $id)
    {
        return $this->redirect(route('patient.update', $id), navigate: true);
        //        $patient = Patient::finddOrFail($id);
        //        $this->form->setPatient($patient);
        //        $this->editMode = true;
        //        $this->addModal = true;
    }

    public function hideAddModal(): void
    {
        $this->addModal = false;
        $this->editMode = false;
        $this->form->reset();
        $this->resetErrorBag();
    }

    #[On('confiirmDelete')]
    public function delete(int $id = 0): void
    {
        $patient = Patient::findOrFail($this->confirmItemId);
        $this->authorize('delete', $patient);
        $patient->delete();
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
        $patient = Patient::withTrashed()->findOrFail($this->confirmItemId);
        $this->authorize('restore', $patient);
        $patient->restore();
        $this->resetConfirmModal();
    }


    public function render()
    {
        return view('livewire.patient.patient-index')->layout('layouts.dash');
    }

    public function getRowDecoration(): array
    {
        return [
            'hover:!bg-accent' => fn($role) => true
        ];
    }

    public function disableInputsWhenEditMode(): bool
    {
        return $this->editMode && !Gate::allows('update', $this->form->patient);
    }

    public function filtersCount(): int
    {
        $filters = [
            $this->searchWord !== '',
            $this->cityId > 0,
            $this->medicalCenterId > 0,
            $this->showArchived === true
        ];
        return count(array_filter($filters));
    }

    public function clearFilters(): void
    {
        $this->cityId = 0;
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
}
// todo remove add and edit modal from here, I created a new component for them
