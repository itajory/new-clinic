<?php

namespace App\Traits;

trait AppointmentTrait
{
    public $medicalCenters;
    public $selectedMedicalCenter;
    public $selectedMedicalCenterObj;
    public $treatments;
    public $selectedTreatment;
    public $doctors;
    public $appointments;
    public $generatedAppointments;
    public $appointmentSpans = [];
    public $selectedDate;
    public $searchPatientWord;
    public $showAddEditAppointmentModal;
    public $isNewPatient;
    public $showAddNewPatient;
    public $showAllDoctors;
    public $patientClass;
    public $appointmentClasss;

    protected function mapDayOfWeek($dayNumber)
    {
        $days = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];

        return $days[$dayNumber];
    }

    public function updatedSelectedMedicalCenter($value)
    {
        $this->selectedMedicalCenterObj = $this->medicalCenters->firstWhere('id', $value);
        $this->loadDoctorsAndAppointments();
    }

    public function updatedSelectedTreatment()
    {
        $this->loadDoctorsAndAppointments();
    }

    public function updatedSelectedDate()
    {
        $this->loadDoctorsAndAppointments();
    }

    public function updatedShowAllDoctors()
    {
        $this->loadDoctorsAndAppointments();
    }
}
