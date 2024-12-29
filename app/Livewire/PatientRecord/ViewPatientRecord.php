<?php

namespace App\Livewire\PatientRecord;

use Livewire\Component;
use App\Models\PatientRecord;

class ViewPatientRecord extends Component
{
    public array $headers;
    public  $records = null;
    public $patientId;
    public function mount($patientId)
    {
        $this->patientId = $patientId;
        $this->getPatientRecords($patientId);

        $this->headers = [
            ['key' => 'id', 'label' => trans('ID')],
            ['key' => 'treatment', 'label' => trans('Treatment')],
            ['key' => 'doctor', 'label' => trans('Doctor')],
            ['key' => 'appointment_id', 'label' => trans('Appointment')],
            ['key' => 'medicalCenter', 'label' => trans('Medical Center')],
            ['key' => 'description', 'label' => trans('Description')],
            ['key' => 'created_at', 'label' => trans('Date')],
        ];
    }
    public function render()
    {
        return view('livewire.patient-record.view-patient-record');
    }

    public function getPatientRecords($patientId)
    {
        $this->records = PatientRecord::where('patient_id', $patientId)->with('treatment', 'doctor', 'medicalCenter')->orderBy('created_at', 'desc')->get();
    }
}
