<?php

namespace App\Livewire\MedicalCenter;

use App\Models\City;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\MedicalCenter;
use Illuminate\Support\Facades\Cache;
use App\Livewire\Forms\MedicalCenterForm;
use Illuminate\Database\Eloquent\Collection;

class MedicalCenterIndex extends Component
{
    public MedicalCenterForm $form;
    public array $headers;
    public string $searchWord = '';
    public bool $addModal = false;
    public bool $editMode = false;
    public Collection $medicalCenters;
    public Collection $cities;
    public bool $showArchived = false;

    /// for confirmation modal
    public bool $showConfirmModal = false;

    public $confirmMessage = '';
    public bool $isDelete = false;
    public $confirmItemId = 0;

    public function save(): void
    {
        if ($this->editMode) {
            $this->authorize('update', $this->form->medicalCenter);
            $this->form->update();
        } else {
            $this->authorize('create', MedicalCenter::class);
            $this->form->store();
        }
        $this->hideModal();
        $this->refresCache();
    }

    public function mount()
    {
        $this->authorize('viewAny', MedicalCenter::class);
        $this->headers = [
            ['key' => 'id', 'label' => __('id')],
            ['key' => 'name', 'label' => __('name')],
            ['key' => 'phone', 'label' => __('phone')],
            ['key' => 'fax', 'label' => __('fax')],
            ['key' => 'whatsapp', 'label' => __('whatsapp')],
            ['key' => 'email', 'label' => __('email')],
            ['key' => 'city.name', 'label' => __('city')],
        ];
        $this->cities = City::all();
    }

    public function search(): void
    {
        $this->medicalCenters = MedicalCenter::where(function ($query) {
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
        return view('livewire.medical-center.medical-center-index')->layout('layouts.dash');
    }

    public function showAddModal(): void
    {
        $this->addModal = true;
    }

    public function edit(int $id): void
    {
        $medicalCenter = MedicalCenter::findOrFail($id);
        $this->form->setMedicalCenter($medicalCenter);
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
        $medicalCenter = MedicalCenter::findOrFail($this->confirmItemId);
        $this->authorize('delete', $medicalCenter);
        $medicalCenter->delete();
        $this->refresCache();
        $this->resetConfirmModal();
    }

    #[On('confirmResetore')]
    public function restore(int $id = 0)
    {
        $medicalCenter = MedicalCenter::withTrashed()->findOrFail($this->confirmItemId);
        $this->authorize('restore', $medicalCenter);
        $medicalCenter->restore();
        $this->refresCache();
        $this->resetConfirmModal();
    }

    public function getRowDecoration(): array
    {
        return [
            'hover:!bg-accent' => fn($role) => true
        ];
    }

    public function refresCache()
    {
        Cache::forget('medical_centers');
        Cache::remember('medical_centers', 60 * 60, function () {
            return MedicalCenter::all();
        });
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
