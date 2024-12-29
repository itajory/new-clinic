<?php

namespace App\Livewire\Patient;

// namespace App\Livewire\Patient;

use App\Models\City;
use App\Models\Patient;
use App\Models\PatientDoc;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use App\Livewire\Forms\PatientDocForm;
use Illuminate\Support\Facades\Storage;
use Mary\Traits\Toast;

class ViewPatient extends Component
{
    use WithFileUploads, Toast;

    public $patient;
    public PatientDocForm $addFileFolderForm;
    public bool $showAddFileModal = false;
    public bool $editMode = false;

    public ?int $currentFolderId = null;
    public ?bool $isNested = false;
    public int $notPaidCost = 0;
    public array $tabs = [
        // 'personal_info' => [
        //     'title' => 'Personal Info',
        //     'icon' => '              <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
        //                         <path d="M3 3v5h5"/>
        //                         <path d="M12 7v5l4 2"/>',
        // ],
        'finanical_report' => [
            'title' => 'Fiancial Report',
            'icon' => '<path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 18V6"/>',
        ],
        'patient_funds' => [
            'title' => 'Patient Funds',
            'icon' => '       <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                            <path d="M12 5 9.04 7.96a2.17 2.17 0 0 0 0 3.08c.82.82 2.13.85 3 .07l2.07-1.9a2.82 2.82 0 0 1 3.79 0l2.96 2.66"/>
                            <path d="m18 15-2-2"/>
                            <path d="m15 18-2-2"/>',
        ],
        'history' => [
            'title' => 'History',
            'icon' => '      <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
            <path d="M3 3v5h5"/>
            <path d="M12 7v5l4 2"/>',
        ],
        'docs' => [
            'title' => 'Documents',
            'icon' => '   <path d="M20 7h-3a2 2 0 0 1-2-2V2"/>
                            <path d="M9 18a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h7l4 4v10a2 2 0 0 1-2 2Z"/>
                            <path d="M3 7.6v12.8A1.6 1.6 0 0 0 4.6 22h9.8"/>',
        ],
    ];
    public string $activeTab = 'financial_report';
    public string $documentType = '';
    public ?object $currentFolderObj = null;

    public ?int $returnedChecksCount = 0;

    /// for confirmation modal
    public bool $showConfirmModal = false;

    public $confirmMessage = '';
    public bool $isDelete = false;
    public $confirmItemId = 0;
    public $successMessage = '';

    public function mount(int $id, ?bool $isNested = false): void
    {
        $this->activeTab = auth()->user()->role_id == 2 ? 'history' : 'finanical_report';
        $this->loadPatientData($id);
        $this->isNested = $isNested;
        $this->successMessage = __('success');
    }

    public function render()
    {
        return view('livewire.patient.view-patient')
            ->with([
                'activeTab' => $this->activeTab,
                'tabs' => $this->tabs,
                'patient' => $this->patient,
            ])
            ->layout('layouts.dash');
    }

    public function loadPatientData(int $id)
    {
        $patient = Patient::findOrFail($id);
        $this->authorize('view', $patient);
        $this->patient = $patient;

        $this->notPaidCost = $patient->appointments()->notPaidAndCompleted()->sum('total');
        $this->returnedChecksCount = $patient->checks()->where('status', 'returned')->count();
    }

    public function setActiveTab(string $tab)
    {
        $this->activeTab = $tab;
    }


    ///// add file modal

    public function setShowAddFileModal(bool $value, string $type = "file", bool $update = false, $item = null): void
    {
        if ($update && $item) {
            $doc = PatientDoc::findOrFail($item);
            $this->addFileFolderForm->setPatientDoc($doc, $type);
            $this->editMode = true;
        } elseif ($value) {
            $this->addFileFolderForm->initForm($this->patient->id, $type, $this->currentFolderId);
        } else {
            $this->addFileFolderForm->reset();
            $this->editMode = false;
        }
        $this->documentType = $type;
        $this->showAddFileModal = $value;
    }

    public function setCurrentFolderId(int $currentFolderId): void
    {
        $this->currentFolderId = $currentFolderId;
    }

    public function getCurrentFolderId(): int
    {
        return $this->currentFolderId;
    }

    public function saveFileFolder(): void
    {
        if ($this->editMode) {
            $this->addFileFolderForm->update();
            $this->setShowAddFileModal(false);
            $this->loadPatientData($this->patient->id);

        } else {

            $this->addFileFolderForm->store($this->currentFolderId);
            $this->setShowAddFileModal(false);
        }
        //        $this->loadPatientData($this->patient->id);
    }

    public function getFoldersTree()
    {
        if ($this->currentFolderId == (null || 0)) {
            return [];
        }
        $tree = $this->patient->documents()->find($this->currentFolderId)->parentsArray();
        $tree[] = $this->currentFolderObj;
        return $tree;
    }

    public function getFolderChildren()
    {
        if ($this->currentFolderId == (null || 0)) {

            return $this->patient->documents()->where('parent_id', null)->get();
        }
        return $this->patient->documents()->find($this->currentFolderId)->children;
    }

    public function setCurrentFolder($id): void
    {
        $this->currentFolderId = $id;
        $this->currentFolderObj = $id == (null || 0) ? null : $this->patient->documents()->find($this->currentFolderId);
        //        $this->getFoldersTree();
    }

    public function downloadDoc($file)
    {
        return response()->download(storage_path('app/patient-docs/' . $file['patient_id'] . '/' . $file['path']));
    }

    public function goBackFiles(): void
    {
        if (!$this->currentFolderObj) {
            return;
        }

        $parentId = $this->currentFolderObj->parent_id ?? null;
        $this->setCurrentFolder($parentId);
    }

    // #[On('filterFinances')]
    // public function handleFinanceFilterChanges($dateFrom = null, $dateTo = null, $checkStatus = null, $filtersCount): void
    // {
    //     $this->filtersCount = $filtersCount;
    //     $this->showFilterDrawer = false;
    // }


    public function changeShowConfirmModal($showModal, $message, $isDelete = false, $confirmItemId = 0): void
    {
        $this->showConfirmModal = $showModal;
        $this->confirmMessage = $message;
        $this->isDelete = $isDelete;
        $this->confirmItemId = $confirmItemId;
    }

    #[On('confiirmDelete')]
    public function delete(int $id = 0): void
    {
        $file = PatientDoc::findOrFail($this->confirmItemId);
        if ($file->children()->count() > 0) {
            $this->warning(
                __('You can not delete this file because it has sub files'),
                position: 'bottom-end',
                icon: 'c-trash',
                css: 'bg-warning text-white'
            );
            $this->resetConfirmModal();
            return;
        }
        if ($file->path != null) {
            Storage::delete('patient-docs/' . $file->patient_id . '/' . $file->path);

        }
        $file->delete();

        $this->warning(
            $this->successMessage,
            position: 'bottom-end',
            icon: 'c-trash',
            css: 'bg-warning text-white'
        );
        $this->resetConfirmModal();
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
