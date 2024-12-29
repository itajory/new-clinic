<?php

namespace App\Livewire\Appointment\Traits;

use App\Models\MedicalCenter;

trait WorkingHoursHandlerTrait
{
    public function setMedicalCenter($medicalCenter)
    {
        $this->selectedMedicalCenterObj = MedicalCenter::findOrFail($medicalCenter['id']);
        $this->selectedMedicalCenter = $medicalCenter['id'];
        $this->loadDoctorsAndAppointments();
    }

    public function setTreatment($id)
    {
        $this->selectedTreatment = $id == 0 ? null : $id;
        $this->loadDoctorsAndAppointments();
    }

    public function updated($propertyName, $value)
    {
        if ($propertyName === 'showAllDoctors') {
            $this->loadDoctors();
            $this->getGenerateAppointment();
        }
    }
}