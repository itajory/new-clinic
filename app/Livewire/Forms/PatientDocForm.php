<?php

namespace App\Livewire\Forms;

use App\Models\PatientDoc;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PatientDocForm extends Form
{
    use WithFileUploads;

    public ?PatientDoc $patientDoc = null;

    public string $type = '';
    public $path = '';
    public $title = '';
    public $patient_id = 0;
    public $parent_id = 0;


    public function store($parent_id = null)
    {
//        dd($parent_id);
        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'path' => ['nullable', 'file', 'mimes:pdf,docx,doc,jpg,jpeg,png', 'max:5120'],
        ]);
        if ($this->type === 'file') {
            $this->path->store('patient-docs/' . $this->patient_id);
        }

        PatientDoc::create([
            'title' => $this->title,
            'patient_id' => $this->patient_id,
            'parent_id' => $parent_id,
            'path' => $this->path ? $this->path->hashName() : null,
        ]);
    }

    public function initForm(int $patient_id, string $type, ?int $parent_id = null)
    {
        $this->type = $type;
        $this->patient_id = $patient_id;
        $this->parent_id = $parent_id;
    }

    public function setPatientDoc(PatientDoc $patientDoc, string $type)
    {
        $this->patientDoc = $patientDoc;
        $this->title = $patientDoc->title;
        $this->path = $patientDoc->path;
        $this->patient_id = $patientDoc->patient_id;
        $this->parent_id = $patientDoc->parent_id;
        $this->type = $type;
    }

    public function update(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:255'],
//            'path' => ['nullable', 'file', 'mimes:pdf,docx,doc,jpg,jpeg,png', 'max:5120'],
        ]);
//        if ($this->type === 'file' && $this->path != $this->patientDoc->path) {
//            Storage::delete('patient-docs/' . $this->patient_id . '/' . $this->patientDoc->path);
//            $this->path->store('patient-docs/' . $this->patient_id);
//        }
        $this->patientDoc->update([
            'title' => $this->title,
//            'path' => $this->path ? $this->path->hashName() : null,
        ]);
    }


}
