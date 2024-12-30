<?php

namespace App\Services;

use App\Services\Cache\AppointmentCache;
use App\Services\Queries\DoctorQuery;

class AppointmentService
{
    protected $appointmentCache;
    protected $doctorQuery;

    public function __construct(AppointmentCache $appointmentCache, DoctorQuery $doctorQuery)
    {
        $this->appointmentCache = $appointmentCache;
        $this->doctorQuery = $doctorQuery;
    }

    public function getMedicalCenters()
    {
        return $this->appointmentCache->getMedicalCenters();
    }

    public function getTreatments()
    {
        return $this->appointmentCache->getTreatments();
    }

    public function getDoctors($selectedMedicalCenter, $selectedTreatment, $dayOfWeek, $showAllDoctors)
    {
        return $this->doctorQuery->getDoctors($selectedMedicalCenter, $selectedTreatment, $dayOfWeek, $showAllDoctors);
    }

    public function getAppointments($selectedMedicalCenter, $selectedDate)
    {
        return $this->appointmentCache->getAppointments($selectedMedicalCenter, $selectedDate);
    }

    public function getOptimizedAppointments($selectedMedicalCenter, $selectedDate, $doctors)
    {
        return $this->appointmentCache->getOptimizedAppointments($selectedMedicalCenter, $selectedDate, $doctors);
    }
}
