<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Appointment;
use App\Models\PatientRecord;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;

class PattientRecordForm extends Form
{
    public ?PatientRecod $patientRecord = null;
    public string $description = '';
    public int $treatment_id = 0;
    public int $patient_id = 0;
    public int $doctor_id = 0;
    public int $appointment_id = 0;
    public int $medical_center_id = 0;
    public int $prescription_template_id = 0;


    public function rules(): array
    {
        return [
            'description' => ['required', 'string'],
            'treatment_id' => ['required', 'int', 'exists:treatments,id'],
            'patient_id' => ['required', 'int', 'exists:patients,id'],
            'doctor_id' => ['required', 'int', 'exists:users,id'],
            'appointment_id' => ['required', 'int', 'exists:appointments,id'],
            'medical_center_id' => ['required', 'int', 'exists:medical_centers,id']
        ];
    }

    public function store(): void
    {
        $this->validate();
        DB::transaction(function () {
            PatientRecord::create([
                'description' => $this->description,
                'treatment_id' => $this->treatment_id,
                'patient_id' => $this->patient_id,
                'doctor_id' => $this->doctor_id,
                'appointment_id' => $this->appointment_id,
                'medical_center_id' => $this->medical_center_id
            ]);
            $apointment = Appointment::find($this->appointment_id);
            $apointment->update(['status' => 'completed']);
        });
    }

    public function setMainData($appointment): void
    {
        $this->patient_id = $appointment->patient_id;
        $this->doctor_id = $appointment->doctor_id;
        $this->appointment_id = $appointment->id;
        $this->medical_center_id = $appointment->medical_center_id;
        $this->treatment_id = $appointment->treatment_id;
    }
}
