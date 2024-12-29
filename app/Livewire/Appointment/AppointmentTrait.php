<?php

namespace App\Livewire\Appointment;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Treatment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

trait AppointmentTrait
{
    #[On('patient-saved')]
    public function onPatientSaved($patient)
    {
        $this->isNewPatient = false;
        $this->selectedPatient = Patient::find($patient['id']);
    }

    #[On('patient-canceled')]
    public function onPatientCanceled()
    {
        $this->isNewPatient = false;
        $this->selectedPatient = null;
    }

    public function patients()
    {
        return Patient::where(function ($query) {
            $query->whereRaw('LOWER(full_name) LIKE ?', ['%'.strtolower($this->searchPatientWord).'%'])
                  ->orWhere('id', 'like', '%'.$this->searchPatientWord.'%');
        })->get();
    }

    public function selectPatient(Patient $patient)
    {
        $this->selectedPatient = $patient;
        $this->isNewPatient = false;
    }

    public function createNewPatient()
    {
        $this->isNewPatient = true;
        $this->selectedPatient = null;
    }

    public function changeShowAddEditAppointmentModal($appointment = null, $row = null, $doctor = null)
    {
        if ($doctor) {
            $this->selectedDoctor = $doctor;
            $this->selectedDoctorTreatment = Cache::remember(
                "treatment_{$doctor['treatment_id']}",
                3600,
                fn () => Treatment::find($doctor['treatment_id'])
            );
        }

        if ($appointment) {
            $this->selectedAppointment = Appointment::with([
                'patient:id,full_name',
                'doctor:id,name,treatment_id',
            ])->findOrFail($appointment['id']);

            $this->selectedPatient = $this->selectedAppointment->patient;
            $this->selectedDoctor = $this->selectedAppointment->doctor;
            $this->selectedDoctorTreatment = $this->selectedDoctor->treatment;
        }

        if ($row) {
            $this->selectedTimeSlot = $row;
        }

        $this->showAddEditAppointmentModal = true;
    }

    public function hideAddEditAppointmentModal()
    {
        $this->reset([
            'showAddEditAppointmentModal',
            'selectedAppointment',
            'selectedPatient',
            'isNewPatient',
            'selectedDoctor',
            'selectedDoctorTreatment',
            'selectedTimeSlot',
        ]);
    }

    #[On('hide-add-edit-appointment-modal')]
    public function onAppointmentSaved()
    {
        $this->loadAppointments();
        $this->generateAppointments();
        $this->hideAddEditAppointmentModal();
    }
}