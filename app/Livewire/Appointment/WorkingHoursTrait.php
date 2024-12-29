<?php

namespace App\Livewire\Appointment;

use App\Models\MedicalCenter;
use App\Models\User;
use Carbon\Carbon;

trait WorkingHoursTrait
{
    public function setMedicalCenter($medicalCenter)
    {
        $this->selectedMedicalCenterObj = MedicalCenter::findOrFail($medicalCenter['id']);
        $this->selectedMedicalCenter = $medicalCenter['id'];
        $this->loadDoctors();
        $this->loadAppointments();
        $this->generateAppointments();
    }

    public function setTreatment($id)
    {
        $this->selectedTreatment = $id == 0 ? null : $id;
        $this->loadDoctors();
        $this->loadAppointments();
        $this->generateAppointments();
    }

    public function updated($propertyName, $value)
    {
        if ($propertyName === 'showAllDoctors') {
            $this->loadDoctors();
            $this->getGenerateAppointment();
        }
    }
}