<?php

namespace App\Livewire\Treatment;

use App\Models\MedicalCenter;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use App\Models\Treatment;
use Livewire\Attributes\On;
use App\Livewire\Forms\TreatmentForm;
use Illuminate\Database\Eloquent\Collection;

class TreatmentIndex extends Component
{
    public TreatmentForm $form;
    public array $headers;
    public string $searchWord = '';
    public bool $addModal = false;
    public bool $editMode = false;
    public bool $showArchived = false;
    public Collection $treatments;
    public array $durations;

    /// for confirmation modal
    public bool $showConfirmModal = false;

    public $confirmMessage = '';
    public bool $isDelete = false;
    public $confirmItemId = 0;

    public function save(): void
    {
        if ($this->editMode) {
            $this->authorize('update', $this->form->treatment);
            $this->form->update();
        } else {
            $this->authorize('create', Treatment::class);
            $this->form->store();
        }
        $this->hideModal();
        $this->refresCache();
    }

    public function mount()
    {
        $this->headers = [
            ['key' => 'id', 'label' => __('id')],
            ['key' => 'name', 'label' => __('name')],
            ['key' => 'price', 'label' => __('price')],
            ['key' => 'duration', 'label' => __('duration')],
        ];

        //        $this->durations = [
        //         ['id'=>15, 'name'=>'15'],
        //         ['id'=>30, 'name'=>'30'],
        //         ['id'=>45, 'name'=>'45'],
        //         ['id'=>60, 'name'=>'60'],
        //         ['id'=>75, 'name'=>'75'],
        //         ['id'=>90, 'name'=>'90'],
        //         ['id'=>105, 'name'=>'105'],
        //         ['id'=>120, 'name'=>'120'],
        //         ['id'=>135, 'name'=>'135'],
        //         ['id'=>150, 'name'=>'150'],
        //         ['id'=>165, 'name'=>'165'],
        //         ['id'=>180, 'name'=>'180'],
        //        ];
        $this->durations = [
            15,
            30,
            45,
            60,
            75,
            90,
            105,
            120,
            135,
            150,
            165,
            180
        ];
    }

    public function search(): void
    {
        $this->treatments = Treatment::where(function ($query) {
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
        return view('livewire.treatment.treatment-index')
            ->layout('layouts.dash');
    }

    public function showAddModal(): void
    {
        $this->addModal = true;
    }

    public function edit(int $id): void
    {
        $treatment = Treatment::findOrFail($id);
        $this->form->setTreatment($treatment);
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
        $treatment = Treatment::findOrFail($this->confirmItemId);

        $this->authorize('delete', $treatment);
        $treatment->delete();
        $this->resetConfirmModal();
        $this->refresCache();
    }

    #[On('confirmResetore')]
    public function restore(int $id = 0): void
    {
        $treatment = Treatment::withTrashed()->findOrFail($this->confirmItemId);
        $this->authorize('restore', $treatment);
        $treatment->restore();
        $this->resetConfirmModal();
        $this->refresCache();
    }

    public function forceDelete(int $id): void
    {
        $treatment = Treatment::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $treatment);
        $treatment->forceDelete();
        $this->refresCache();
    }


    public function getRowDecoration(): array
    {
        return [
            'hover:!bg-accent' => fn($role) => true
        ];
    }

    public function changeShowConfirmModal($showModal, $message, $isDelete = false, $confirmItemId = 0): void
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


    public function refresCache()
    {
        Cache::forget('treatments');
        Cache::remember('treatments', 60 * 60, function () {
            return Treatment::all();
        });
    }

}
