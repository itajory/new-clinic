<?php

namespace App\Livewire\Appointment;

use App\Livewire\Appointment\Traits\AppointmentGeneratorTrait;
use App\Livewire\Appointment\Traits\AppointmentHandlerTrait;
use App\Livewire\Appointment\Traits\DateHandlerTrait;
use App\Livewire\Appointment\Traits\WorkingHoursHandlerTrait;
use App\Models\Appointment;
use App\Models\Patient;
use App\Services\AppointmentService;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class AppointmentIndex extends Component
{
    use AppointmentHandlerTrait;
    use DateHandlerTrait;
    use WorkingHoursHandlerTrait;
    use AppointmentHandlerTrait;
    use AppointmentGeneratorTrait;
    use DateHandlerTrait;
    use WorkingHoursHandlerTrait;

    private const DATE_FORMAT = 'm/d/Y';

    public $patientClass;
    public $appointmentClasss;
    public $selectedDate = '';
    public $selectedMedicalCenter;
    public $selectedMedicalCenterObj;
    public $selectedTreatment;
    public $selectedDoctor;
    public $selectedDoctorTreatment;
    public string $searchPatientWord = '';

    public $medicalCenters = [];
    public $treatments = [];
    public $doctors = [];
    public $generatedAppointments = [];
    public $appointmentSpans = [];
    public $appointments = [];

    public $showAddEditAppointmentModal = false;
    public $selectedAppointment;
    public $isNewPatient = false;
    public $showAddNewPatient = false;
    public $showAllDoctors = true;
    public $selectedPatient;
    public $selectedTimeSlot;

    private $selectedDateCarbon;
    private $appointmentService;

    public function boot(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    public function mount()
    {
        $this->authorize('viewAny', Appointment::class);
        $this->initializeData();
    }

    private function initializeData()
    {
        $this->patientClass = Patient::class;
        $this->appointmentClasss = Appointment::class;
        $this->selectedDate = now()->format(self::DATE_FORMAT);

        $this->medicalCenters = $this->appointmentService->getMedicalCenters();
        $this->selectedMedicalCenter = $this->medicalCenters[0]->id;
        $this->selectedMedicalCenterObj = $this->medicalCenters[0];

        $this->treatments = $this->appointmentService->getTreatments();
        $this->loadDoctorsAndAppointments();
    }

    private function loadDoctorsAndAppointments()
    {
        $this->doctors = $this->appointmentService->getDoctors(
            $this->selectedMedicalCenter,
            $this->selectedTreatment,
            $this->mapDayOfWeek(Carbon::parse($this->selectedDate)->dayOfWeek),
            $this->showAllDoctors
        );

        $this->appointments = $this->appointmentService->getAppointments(
            $this->selectedMedicalCenter,
            $this->selectedDate
        );

        $this->generateAppointments();
    }

    #[On('echo:appointments,AppointmentUpdated')]
    public function getGenerateAppointment()
    {
        $this->appointments = $this->appointmentService->getOptimizedAppointments(
            $this->selectedMedicalCenter,
            $this->selectedDate,
            $this->doctors
        );
        $this->generateAppointments();
    }

    public function isTimeSlotInDoctorWorkingHours($timeSlot, $medicalCenter, $dayOfWeek, $doctor)
    {
        $timeSlot = Carbon::createFromFormat('g:i A', $timeSlot);
        $dayOfWeek = $this->mapDayOfWeek($dayOfWeek);

        $doctorSchedule = $doctor
            ->doctorSchedule()
            ->where('medical_center_id', $medicalCenter->id)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$doctorSchedule) {
            return false;
        }

        $doctorStartTime = Carbon::parse($doctorSchedule->start_time);
        $doctorEndTime = Carbon::parse($doctorSchedule->end_time)->subMinutes(15);

        return $timeSlot->between($doctorStartTime, $doctorEndTime);
    }

    public function render()
    {
        return view('livewire.appointment.appointment-index')->layout('layouts.dash');
    }
}
