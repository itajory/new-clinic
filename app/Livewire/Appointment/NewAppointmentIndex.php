<?php

namespace App\Livewire\Appointment;

use App\Events\AppointmentUpdated;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Treatment;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\User;
use App\Models\MedicalCenter;
use Carbon\Carbon;

class NewAppointmentIndex extends Component
{
//    protected $listeners = ['AppointmentUpdated' => '$refresh'];

    private const DATE_FORMAT = 'm/d/Y';
    public $selectedDate = '';
    public $medicalCenters;
    public int $selectedMedicalCenterIndex = 0;
    public bool $showAllDoctors = false;
    public $doctors = [];
    public $slots = [];
//    public $patientClass;
    public $appointmentClasss;
    public string $searchPatientWord = '';
    public $selectedPatient;
    public $isNewPatient = false;
    public $selectedAppointment;
    /**
     * @var \Illuminate\Support\HigherOrderCollectionProxy|mixed
     */
    public $selectedDoctor;
    public $selectedDoctorTreatment;
    public $selectedTimeSlot;
    /**
     * @var true
     */
    public bool $showAddEditAppointmentModal;
    public $selectedMedicalCenterObj;
    public $durations = [15, 30, 45, 60, 75, 90, 105, 120, 135, 150, 165, 180];

    public function mount()
    {
        $this->selectedDate = now()->format(self::DATE_FORMAT);
        $this->fetchMedicalCenters();
//        $this->selectMedicalCenter($this->selectedMedicalCenterIndex);
//        dd($this->medicalCenters);
    }

    public function fetchMedicalCenters()
    {
        $dayOfWeek = Carbon::parse($this->selectedDate)->dayOfWeek;
        // Convert Sunday from 0 to 7 if needed
        $dayOfWeek = $dayOfWeek === 0 ? 7 : $dayOfWeek;
        $this->medicalCenters = MedicalCenter::with([
            'workingHours' => function ($query) use ($dayOfWeek) {
                $query->where('day_of_week', $dayOfWeek);
            },
            'doctors' => function ($query) {
                $query->whereHas('role', function ($roleQuery) {
                    $roleQuery->where('name', 'doctor');
                });
            },
            'doctors.appointments' => function ($query) {
                $query->whereDate('appointment_time',
                    Carbon::createFromFormat(self::DATE_FORMAT, $this->selectedDate)->format('Y-m-d'))
                    ->with('patient:id,full_name');
            },
            'doctors.doctorSchedule' => function ($query) use ($dayOfWeek) {
                $query->where('day_of_week', $dayOfWeek);
            }
        ])
            ->get()
            ->map(function ($medicalCenter) {
                $medicalCenter->doctors = $medicalCenter->doctors->map(function ($doctor) {
                    return [
                        'id' => $doctor->id,
                        'name' => $doctor->name,
                        'schedule' => $doctor->doctorSchedule->first(),
                        'appointments' => $doctor->appointments ? $doctor->appointments->map(function ($appointment) {
                            return [
                                'id' => $appointment->id,
                                'appointment_time' => $appointment->appointment_time,
                                'duration' => $appointment->duration,
                                'patient' => $appointment->patient
                            ];
                        }) : []
                    ];
                });
                return $medicalCenter;
            });
    }

    #[On('echo:appointments,AppointmentUpdated')]
    public function updatedSelectedDate($date)
    {
        $this->fetchMedicalCenters();
        $this->dispatch('appointments-changed', $this->medicalCenters);
        $this->dispatch('date-changed', $date);

//        $this->dispatch('AppointmentUpdated');
//        dd($this->medicalCenters);
    }


    public function getSelectedDate()
    {
        return $this->selectedDate;
    }

    public function render()
    {
        return view('livewire.appointment.new-appointment-index', [
            'medicalCenters' => $this->medicalCenters,
            'patientClass' => Patient::class,
        ])->layout('layouts.dash');
    }

    public function patients()
    {
        return Patient::where(function ($query) {
            $query
                ->whereRaw('LOWER(full_name) LIKE ?', [
                    '%' . strtolower($this->searchPatientWord) . '%',
                ])
                ->orWhere('id', 'like', '%' . $this->searchPatientWord . '%');
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

    public function changeShowAddEditAppointmentModal(
        $appointment = null,
        $row = null,
        $doctor = null,
        $selectedMedicalCenterObj = null
    ) {
        $this->selectedMedicalCenterObj = $selectedMedicalCenterObj;
        if ($doctor) {
            $this->selectedDoctor = $doctor;
            $this->selectedDoctorTreatment = Cache::remember(
                "treatment_{$doctor['treatment_id']}",
                60 * 60,
                fn() => Treatment::find($doctor['treatment_id'])
            );
        }

        if ($appointment) {
            $this->selectedAppointment = Appointment::with([
                'patient:id,full_name',
                'doctor:id,name,treatment_id'
            ])->find($appointment['id']);
            $this->selectedPatient = $this->selectedAppointment->patient;
            $this->selectedDoctor = $this->selectedAppointment->doctor;
            $this->selectedDoctorTreatment = $this->selectedDoctor->treatment;
        }
        if ($row) {
            $this->selectedTimeSlot = $row;
        }
        $this->showAddEditAppointmentModal = true;
    }

    #[On('hide-add-edit-appointment-modal')]
    public function hideAddEditAppointmentModal()
    {
        $this->hideAddEditAppointmentModal1();
//        $this->fetchMedicalCenters(); // Re-fetch medical centers to re-render the necessary components
//        $this->dispatch('appointments-changed',
//            $this->medicalCenters); // Dispatch event to re-render the necessary components

        event(new AppointmentUpdated());
    }

    public function hideAddEditAppointmentModal1()
    {
        $this->showAddEditAppointmentModal = false;
        $this->selectedAppointment = null;
        $this->selectedPatient = null;
        $this->isNewPatient = false;
        $this->selectedDoctor = null;
        $this->selectedDoctorTreatment = null;
        $this->selectedTimeSlot = null;
    }

    public function setWaiting($appointmentId)
    {

        $selectedAppointment = Appointment::find($appointmentId);
        $this->authorize('update', $selectedAppointment);
        $selectedAppointment->status = 'waiting';
        $selectedAppointment->save();
//        $this->fetchMedicalCenters();
//        $this->dispatch('appointments-changed', $this->medicalCenters);
        event(new AppointmentUpdated());
    }
}
