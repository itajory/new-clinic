<?php

namespace App\Livewire\Appointment;

use App\Models\Appointment;
use App\Models\MedicalCenter;
use App\Models\Patient;
use App\Models\Treatment;
use App\Models\User;
use App\Services\AppointmentService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;

class AppointmentIndex extends Component
{
    private const DATE_FORMAT = 'm/d/Y';
    private const CACHE_DURATION = 3600; // 1 hour in seconds
    private const DOCTORS_CACHE_DURATION = 300; // 5 minutes in seconds

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

    public $durations = [15, 30, 45, 60, 75, 90, 105, 120, 135, 150, 165, 180];
    private $workingHoursCache = [];

    protected $appointmentService;
    private $timeSlotCache = [];

    private $doctorScheduleCache = [];

    public function boot(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    public function mount()
    {
        $this->authorize('viewAny', Appointment::class);
        $this->patientClass = Patient::class;
        $this->appointmentClasss = Appointment::class;
        $this->selectedDate = now()->format(self::DATE_FORMAT);

        $this->initializeData();
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

    public function getSelectedDateCarbon()
    {
        if (!$this->selectedDateCarbon) {
            $this->selectedDateCarbon = Carbon::createFromFormat(self::DATE_FORMAT, $this->selectedDate)->startOfDay();
        }

        return $this->selectedDateCarbon;
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

    public function loadMedicalCenters()
    {
        $this->medicalCenters = Cache::remember('medical_centers', self::CACHE_DURATION, function () {
            return MedicalCenter::select('id', 'name')->get();
        });
    }

    public function loadTreatments()
    {
        $this->treatments = Cache::remember('treatments', self::CACHE_DURATION, function () {
            return Treatment::select('id', 'name')->get();
        });
    }

    public function loadDoctors()
    {
        $dayOfWeek = $this->mapDayOfWeek(Carbon::parse($this->selectedDate)->dayOfWeek);
        $cacheKey = "doctors:{$this->selectedMedicalCenter}:{$this->selectedTreatment}:{$dayOfWeek}:{$this->showAllDoctors}";

        $this->doctors = Cache::remember($cacheKey, self::DOCTORS_CACHE_DURATION, function () use ($dayOfWeek) {
            return User::select('id', 'name', 'treatment_id', 'role_id')
                ->where('role_id', 2)
                ->when($this->selectedMedicalCenter, fn ($q) => $q->whereHas('medicalCenters', fn ($sq) => $sq->where('id', $this->selectedMedicalCenter))
                )
                ->when($this->selectedTreatment, fn ($q) => $q->where('treatment_id', $this->selectedTreatment)
                )
                ->when(!$this->showAllDoctors, fn ($q) => $q->whereHas('doctorSchedule', fn ($sq) => $sq->where('medical_center_id', $this->selectedMedicalCenter)
                           ->where('day_of_week', $dayOfWeek)
                )
                )
                ->with([
                    'medicalCenters:id',
                    'treatment:id,name',
                    'doctorSchedule' => fn ($q) => $q->where('medical_center_id', $this->selectedMedicalCenter),
                ])
                ->get();
        });
    }

    public function selectDate($date)
    {
        $this->loadAppointments();
        $this->generateAppointments();
    }

    public function loadAppointments()
    {
        $date = $this->getSelectedDateCarbon();

        $this->appointments = Appointment::select(
            'id', 'patient_id', 'doctor_id', 'treatment_id',
            'appointment_time', 'duration', 'status'
        )
            ->with([
                'patient:id,full_name',
                'doctor:id,name',
                'treatment:id,name',
            ])
            ->where('medical_center_id', $this->selectedMedicalCenter)
            ->whereRaw('DATE(appointment_time) = ?', [$date->toDateString()])
            ->get();
    }

    public function generateAppointments()
    {
        $timeSlots = $this->generateSlotTimes();
        $appointments = $this->appointments;
        $data = [['Time', ...$this->doctors]];
        $appointmentSpans = [];

        foreach ($timeSlots as $index => $time) {
            if ($time !== 'Time') {
                $row = $this->generateRowForTimeSlot($time, $appointments, $index, $appointmentSpans);
                $data[] = $row;
            }
        }

        $this->generatedAppointments = $data;
        $this->appointmentSpans = $appointmentSpans;

        return $data;
    }

    private function checkWorkingHours($time, $doctor, $dayOfWeek)
    {
        $cacheKey = "working_hours:{$time}:{$doctor->id}:{$dayOfWeek}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($time, $doctor, $dayOfWeek) {
            return [
                'isInMedicalCenterWorkingHours' => $this->isTimeSlotInMedicalCenterWorkingHours(
                    $time,
                    $this->selectedMedicalCenterObj,
                    $dayOfWeek
                ),
                'isInDoctorWorkingHours' => $this->isTimeSlotInDoctorWorkingHours(
                    $time,
                    $this->selectedMedicalCenterObj,
                    $dayOfWeek,
                    $doctor
                ),
            ];
        });
    }

    private function batchCheckWorkingHours($time, $doctors, $dayOfWeek)
    {
        $medicalCenter = $this->selectedMedicalCenterObj;
        $cacheKey = "medical_center_hours:{$time}:{$medicalCenter->id}:{$dayOfWeek}";

        $medicalCenterHours = Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($time, $medicalCenter, $dayOfWeek) {
            return $this->isTimeSlotInMedicalCenterWorkingHours($time, $medicalCenter, $dayOfWeek);
        });

        return collect($doctors)->mapWithKeys(function ($doctor) use ($time, $medicalCenter, $dayOfWeek, $medicalCenterHours) {
            $doctorCacheKey = "doctor_hours:{$time}:{$doctor->id}:{$dayOfWeek}";

            $doctorHours = Cache::remember($doctorCacheKey, self::CACHE_DURATION, function () use ($time, $medicalCenter, $dayOfWeek, $doctor) {
                return $this->isTimeSlotInDoctorWorkingHours($time, $medicalCenter, $dayOfWeek, $doctor);
            });

            return [
                $doctor->id => [
                    'isInMedicalCenterWorkingHours' => $medicalCenterHours,
                    'isInDoctorWorkingHours' => $doctorHours,
                ],
            ];
        })->all();
    }

    private function generateRowForTimeSlot($time, $appointments, $rowIndex, &$appointmentSpans)
    {
        $selectedDate = $this->getSelectedDateCarbon();
        $timeDiff = Carbon::createFromFormat('g:i A', $time);
        $slotTime = $selectedDate->copy()->addHours($timeDiff->hour)->addMinutes($timeDiff->minute)->addSeconds($timeDiff->second);

        $dayOfWeek = $this->mapDayOfWeek($selectedDate->dayOfWeek);
        $workingHoursCache = $this->batchCheckWorkingHours($time, $this->doctors, $dayOfWeek);

        $row = [$time];
        foreach ($this->doctors as $columnIndex => $doctor) {
            $appointment = $this->getAppointmentsForDoctor($doctor, $appointments, $slotTime);
            $workingHours = $workingHoursCache[$doctor->id] ?? ['isInMedicalCenterWorkingHours' => false, 'isInDoctorWorkingHours' => false];

            $rowData = [
                'appointment' => $appointment,
                'isWorkingHours' => $workingHours['isInMedicalCenterWorkingHours'],
                'isDoctorWorkingHours' => $workingHours['isInDoctorWorkingHours'],
                'col_span' => 1,
                'row_span' => 1,
            ];

            if ($appointment) {
                $appointmentDuration = $appointment->duration;
                $rowData['row_span'] = (int) ($appointmentDuration / 15) + ($appointmentDuration % 15 ? 1 : 0);

                if (!isset($appointmentSpans[$columnIndex]) || $appointmentSpans[$columnIndex]['appointment_id'] !== $appointment->id) {
                    $appointmentSpans[$columnIndex] = [
                        'appointment_id' => $appointment->id,
                        'start_row' => $rowIndex,
                        'span' => $rowData['row_span'],
                        'col_span' => 1,
                    ];
                } else {
                    ++$appointmentSpans[$columnIndex]['span'];
                }
            }

            $row[] = $rowData;
        }

        return $row;
    }

    private function getAppointmentsForDoctor($doctor, $appointments, $slotTime)
    {
        return $appointments
            ->where('doctor_id', $doctor->id)
            ->first(function ($appointment) use ($slotTime) {
                $appointmentStart = Carbon::parse($appointment->appointment_time);
                $appointmentEnd = $appointmentStart->copy()->addMinutes($appointment->duration);

                return $slotTime->between($appointmentStart, $appointmentEnd);
            });
    }

    private function findOverlappingAppointments($doctorAppointments, $slotTime)
    {
        return $doctorAppointments

            ->filter(function ($appointment) use ($slotTime) {
                $start = Carbon::parse($appointment->appointment_time);
                $end = $start->copy()->addMinutes($appointment->duration);

                return $slotTime->between($start, $end);
            })
            ->values()
            ->all();
    }

    public function generateSlotTimes()
    {
        $slots = ['Time'];
        $startQuarter = 28;
        $totalQuartersInDay = 96;

        for ($i = 0; $i < $totalQuartersInDay; ++$i) {
            $currentQuarter = ($startQuarter + $i) % $totalQuartersInDay;
            $totalMinutes = $currentQuarter * 15;
            $hours = (int) ($totalMinutes / 60);
            $minutes = $totalMinutes % 60;

            $slots[] = Carbon::createFromTime($hours, $minutes)->format('g:i A');
        }

        return $slots;
    }

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

    private function parseTimeSlot($time)
    {
        return $this->timeSlotCache[$time] ??= Carbon::createFromFormat('g:i A', $time);
    }

    public function isTimeSlotAvailable($time, $doctor)
    {
        $medicalCenter = MedicalCenter::findOrFail($this->selectedMedicalCenter);
        $doctor = User::findOrFail($doctor['id']);
        $treatment = Treatment::findOrFail($doctor['treatment_id']);

        $timeSlot = $this->parseTimeSlot($time);

        $selectedDate = Carbon::createFromFormat(self::DATE_FORMAT, $this->selectedDate);

        if ($timeSlot->lt(now())) {
            return false;
        }

        if (!$this->isTimeSlotInMedicalCenterWorkingHours($time, $medicalCenter, $selectedDate->dayOfWeek)) {
            return false;
        }

        return !Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_time', $selectedDate)
            ->whereTime('appointment_time', $timeSlot->format('H:i:s'))

            ->exists();
    }

    public function isTimeSlotInMedicalCenterWorkingHours($timeSlot, $medicalCenter, $dayOfWeek)
    {
        $cacheKey = "wh_{$medicalCenter->id}_{$dayOfWeek}";
        $workingHours = $this->workingHoursCache[$cacheKey] ??= $medicalCenter->workingHours()
            ->where('day_of_week', $this->mapDayOfWeek($dayOfWeek))
            ->first();

        if (!$workingHours) {
            return false;
        }

        $timeSlot = Carbon::createFromFormat('g:i A', $timeSlot);
        $openTime = Carbon::parse($workingHours->opening_time);
        $closeTime = Carbon::parse($workingHours->closing_time)->subMinutes(15);

        return $timeSlot->between($openTime, $closeTime);
    }

    public function isTimeSlotInDoctorWorkingHours($timeSlot, $medicalCenter, $dayOfWeek, $doctor)
    {
        $timeSlot = Carbon::createFromFormat('g:i A', $timeSlot);
        $dayOfWeek = $this->mapDayOfWeek($dayOfWeek);

        $cacheKey = "ds_{$doctor->id}_{$medicalCenter->id}_{$dayOfWeek}";

        $doctorSchedule = $this->doctorScheduleCache[$cacheKey] ??= $doctor
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
    //    public function isTimeSlotInDoctorWorkingHours($timeSlot, $medicalCenter, $dayOfWeek, $doctor)
    //    {
    //        $timeSlot = Carbon::createFromFormat('g:i A', $timeSlot);
    //
    //        // Correctly map the day of the week
    //        $dayOfWeek = $this->mapDayOfWeek($dayOfWeek);
    //
    //        // Check doctor's schedule
    //        $doctorSchedule = $doctor
    //            ->doctorSchedule()
    //            ->where('medical_center_id', $medicalCenter->id)
    //            ->where('day_of_week', $dayOfWeek)
    //            ->first();
    //
    //        if (!$doctorSchedule) {
    //            return false; // Doctor doesn't work on this day at this medical center
    //        }
    //
    //        $doctorStartTime = Carbon::parse($doctorSchedule->start_time);
    //        $doctorEndTime = Carbon::parse($doctorSchedule->end_time)->subMinutes(15);
    //
    //        // Check if the time slot is within the doctor's working hours
    //        return $timeSlot->between($doctorStartTime, $doctorEndTime);
    //    }

    private function mapDayOfWeek($carbonDayOfWeek)
    {
        return $carbonDayOfWeek === 0 ? 7 : $carbonDayOfWeek;
    }

    public function updatedSelectedDate($date)
    {
        $this->selectDate($date);
    }

    public function isFutureDateTime($time)
    {
        try {
            $timeDiff = Carbon::createFromFormat('g:i A', $time);

            $slotTime = Carbon::parse($this->selectedDate)
                ->setHour($timeDiff->hour)
                ->setMinute($timeDiff->minute)
                ->setSecond($timeDiff->second);

            return $slotTime->greaterThanOrEqualTo(Carbon::now());
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function render()
    {
        return view('livewire.appointment.appointment-index')->layout('layouts.dash');
    }

    public function updated($propertyName, $value)
    {
        if ($propertyName === 'showAllDoctors') {
            $this->loadDoctors();
            $this->getGenerateAppointment();
        }
    }
}
