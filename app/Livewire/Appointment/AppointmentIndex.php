<?php

namespace App\Livewire\Appointment;

use App\Models\Appointment;
use App\Models\Patient;
use App\Services\Appointments\TimeSlotService;
use App\Services\Appointments\WorkingHoursService;
use App\Services\AppointmentService;
use Carbon\Carbon;
use Livewire\Component;

class AppointmentIndex extends Component
{
    use \App\Traits\AppointmentTrait;

    private const DATE_FORMAT = 'm/d/Y';

    protected $appointmentService;
    protected $timeSlotService;
    protected $workingHoursService;

    public function boot(
        AppointmentService $appointmentService,
        TimeSlotService $timeSlotService,
        WorkingHoursService $workingHoursService
    ) {
        $this->appointmentService = $appointmentService;
        $this->timeSlotService = $timeSlotService;
        $this->workingHoursService = $workingHoursService;
    }

    public function mount()
    {
        $this->authorize('viewAny', Appointment::class);
        $this->initializeProperties();
        $this->initializeData();
    }

    private function initializeProperties()
    {
        $this->patientClass = Patient::class;
        $this->appointmentClasss = Appointment::class;
        $this->selectedDate = now()->format(self::DATE_FORMAT);
        $this->searchPatientWord = '';
        $this->showAddEditAppointmentModal = false;
        $this->isNewPatient = false;
        $this->showAddNewPatient = false;
        $this->showAllDoctors = true;
    }

    private function initializeData()
    {
        $this->medicalCenters = $this->appointmentService->getMedicalCenters();
        $this->selectedMedicalCenter = $this->medicalCenters[0]->id;
        $this->selectedMedicalCenterObj = $this->medicalCenters[0];
        $this->treatments = $this->appointmentService->getTreatments();
        $this->loadDoctorsAndAppointments();
    }

    private function loadDoctorsAndAppointments()
    {
        $dayOfWeek = $this->mapDayOfWeek(Carbon::parse($this->selectedDate)->dayOfWeek);

        $this->doctors = $this->appointmentService->getDoctors(
            $this->selectedMedicalCenter,
            $this->selectedTreatment,
            $dayOfWeek,
            $this->showAllDoctors
        );

        $this->appointments = $this->appointmentService->getOptimizedAppointments(
            $this->selectedMedicalCenter,
            $this->selectedDate,
            $this->doctors
        );

        $this->generateAppointments();
    }

    public function generateAppointments()
    {
        $timeSlots = $this->timeSlotService->generateSlotTimes();
        $dayOfWeek = $this->mapDayOfWeek(Carbon::parse($this->selectedDate)->dayOfWeek);

        $workingHoursCache = $this->workingHoursService->batchCheckWorkingHours(
            $timeSlots,
            $this->doctors,
            $dayOfWeek,
            $this->selectedMedicalCenter,
            $this->selectedMedicalCenterObj
        );

        $this->generatedAppointments = $this->timeSlotService->generateAppointmentGrid(
            $timeSlots,
            $this->doctors,
            $this->appointments,
            $workingHoursCache,
            $this->appointmentSpans
        );
    }

    public function render()
    {
        return view('livewire.appointment.appointment-index')->layout('layouts.dash');
    }
}
