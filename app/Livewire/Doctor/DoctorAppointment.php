<?php

namespace App\Livewire\Doctor;

use Carbon\Carbon;
use Mary\Traits\Toast;
use Livewire\Component;
use App\Models\Appointment;
use Livewire\Attributes\On;
use App\Models\MedicalCenter;
use App\Models\PatientRecord;
use App\Events\AppointmentUpdated;
use App\Models\PrescriptionTemplate;
use Illuminate\Support\Facades\Cache;
use App\Livewire\Forms\PattientRecordForm;
use Illuminate\Database\Eloquent\Collection;

class DoctorAppointment extends Component
{
    use Toast;

    private const DATE_FORMAT = "m/d/Y";
    public $selectedDate = "";
    public PattientRecordForm $form;
    public Collection $prescriptions;
    // public Collection $treatments;


    public $doctor;
    public $medicalCenters;
    public $appointments = [];

    public $selectedMedicalCenter = null;
    public $selectedAppointment = null;
    public $selectedMedicalCenterObj = null;
    public $generatedAppointments = [];
    public $appointmentSpans = [];

    public string $successMessage = '';

    public bool $showApproveOrCancelAdd = false;

    public function mount($doctor)
    {
        $this->doctor = $doctor;
        $this->loadMedicalCenters();
        $this->selectedMedicalCenter = $this->medicalCenters[0]->id;
        $this->selectedMedicalCenterObj = $this->medicalCenters[0];
        $this->selectedDate = Carbon::now()->format(self::DATE_FORMAT);
        $this->loadAppointments();
        $this->generateAppointments();
        // $this->getGenerateAppointment();
        $this->successMessage = 'Appointment closed successfully';
        $this->prescriptions = $this->getPrescriptions();
        // dd($this->selectedDate);
    }

    #[On('echo:appointments,AppointmentUpdated')]
    public function getGenerateAppointment()
    {
        $this->loadAppointments();
        $this->generateAppointments();
//        $this->dispatch('appointmentsUpdated');
    }

    public function render()
    {
        return view('livewire.doctor.doctor-appointment', [
            "appointmentClasss" => Appointment::class,
        ]);
    }

    public function loadMedicalCenters(): void
    {
        $this->medicalCenters = $this->doctor->medicalCenters;
    }

    public function selectDate($date)
    {
        // dd($date);
        // $this->selectedDate = // date(self::DATE_FORMAT, $date / 1000);
        $this->loadAppointments();
        $this->generateAppointments();
    }

    public function loadAppointments()
    {
        $date = Carbon::createFromFormat(
            self::DATE_FORMAT,
            $this->selectedDate
        )->startOfDay();

        $this->appointments = Appointment::with(['patient', 'doctor', 'treatment'])
            ->whereDate("appointment_time", $date)
            ->where("medical_center_id", $this->selectedMedicalCenter)
            ->get();
    }

    public function generateAppointments()
    {
        $timeSlots = $this->generateSlotTimes();
        $appointments = $this->appointments;
        $data = [["Time", $this->doctor->name]];

        $appointmentSpans = [];

        foreach ($timeSlots as $index => $time) {
            if ($time !== "Time") {
                $row = $this->generateRowForTimeSlot(
                    $time,
                    $appointments,
                    $index,
                    $appointmentSpans
                );
                $data[] = $row;
            }
        }
        $this->generatedAppointments = $data;
        $this->appointmentSpans = $appointmentSpans;
        return $data;
    }

    private function generateRowForTimeSlot($time, $appointments, $rowIndex, &$appointmentSpans)
    {
        $row = [$time];
        $selectedDate = Carbon::createFromFormat(self::DATE_FORMAT, $this->selectedDate)->startOfDay();
        $timeDiff = Carbon::createFromFormat("g:i A", $time);
        $slotTime = $selectedDate->copy()->addHours($timeDiff->hour)->addMinutes($timeDiff->minute)->addSeconds($timeDiff->second);

        // Correctly map the day of the week
        $dayOfWeek = $this->mapDayOfWeek($selectedDate->dayOfWeek);

        $appointment = $this->getAppointmentsForDoctor($this->doctor, $appointments, $slotTime);

        $isInMedicalCenterWorkingHours = $this->isTimeSlotInMedicalCenterWorkingHours($time,
            $this->selectedMedicalCenterObj, $dayOfWeek);
        $isInDoctorWorkingHours = $this->isTimeSlotInDoctorWorkingHours($time, $this->selectedMedicalCenterObj,
            $dayOfWeek, $this->doctor);

        if ($appointment) {
            $appointmentDuration = $appointment->duration;
            $rowSpan = ceil($appointmentDuration / 15);  // Assuming 15-minute intervals

            if (!isset($appointmentSpans[0]) || $appointmentSpans[0]['appointment_id'] !== $appointment->id) {
                $appointmentSpans[0] = [
                    'appointment_id' => $appointment->id,
                    'start_row' => $rowIndex,
                    'span' => $rowSpan,
                    'col_span' => 1  // Assuming col_span is always 1 for vertical merging
                ];
            } else {
                $appointmentSpans[0]['span']++;
            }

            $row[] = [
                'appointment' => $appointment,
                'isWorkingHours' => $isInMedicalCenterWorkingHours,
                'isDoctorWorkingHours' => $isInDoctorWorkingHours,
                'col_span' => 1,  // Assuming col_span is always 1 for vertical merging
                'row_span' => $rowSpan
            ];
        } else {
            $row[] = [
                'appointment' => null,
                'isWorkingHours' => $isInMedicalCenterWorkingHours,
                'isDoctorWorkingHours' => $isInDoctorWorkingHours,
                'col_span' => 1,
                'row_span' => 1
            ];
        }

        return $row;
    }


    private function getAppointmentsForDoctor($doctor, $appointments, $slotTime)
    {
        $doctorAppointments = $appointments->where("doctor_id", $doctor->id);
        $slotAppointments = $this->findOverlappingAppointments(
            $doctorAppointments,
            $slotTime
        );

        return !empty($slotAppointments) ? $slotAppointments[0] : null;
    }

    private function findOverlappingAppointments($doctorAppointments, $slotTime)
    {
        $slotAppointments = [];

        foreach ($doctorAppointments as $appointment) {
            $appointmentStart = Carbon::parse($appointment->appointment_time);
            $appointmentEnd = $appointmentStart
                ->copy()
                ->addMinutes($appointment->duration);

            if (
                $slotTime->greaterThanOrEqualTo($appointmentStart) &&
                $slotTime->lessThan($appointmentEnd)
            ) {
                $slotAppointments[] = $appointment;
            }
        }

        return $slotAppointments;
    }

    public function generateSlotTimes()
    {
        $slots = [];
        $slots[] = "Time"; // Assuming you want to keep this header

        // Start from 7 AM, which is 28 quarters from 12 AM
        $startQuarter = 28;
        $totalQuartersInDay = 96; // 24 hours * 4 quarters/hour

        for ($i = 0; $i < $totalQuartersInDay; $i++) {
            $currentQuarter = ($startQuarter + $i) % $totalQuartersInDay;
            $totalMinutes = $currentQuarter * 15;
            $hours = intdiv($totalMinutes, 60);
            $minutes = $totalMinutes % 60;
            $formattedTime = Carbon::createFromTime($hours, $minutes)->format("g:i A");
            $slots[] = $formattedTime;
        }

        return $slots;
    }

    public function setMedicalCenter($medicalCenter)
    {
        $this->selectedMedicalCenterObj = MedicalCenter::find($medicalCenter["id"]);
        $this->selectedMedicalCenter = $medicalCenter["id"];
        $this->loadAppointments();
        $this->generateAppointments();
    }

    public function isTimeSlotInMedicalCenterWorkingHours($timeSlot, $medicalCenter, $dayOfWeek)
    {
        $timeSlot = Carbon::createFromFormat("g:i A", $timeSlot);

        // Correctly map the day of the week
        $dayOfWeek = $this->mapDayOfWeek($dayOfWeek);

        // Find the working hours for this day
        $workingHours = $medicalCenter
            ->workingHours()
            ->where("day_of_week", $dayOfWeek)
            ->first();

        // If no working hours found for this day, the medical center is closed
        if (!$workingHours) {
            return false;
        }

        $openTime = Carbon::parse($workingHours->opening_time);
        $closeTime = Carbon::parse($workingHours->closing_time)->subMinutes(15); // Adjusted close time

        // Check if the time slot is within working hours
        return $timeSlot->between($openTime, $closeTime);
    }

    public function isTimeSlotInDoctorWorkingHours($timeSlot, $medicalCenter, $dayOfWeek, $doctor)
    {
        $timeSlot = Carbon::createFromFormat("g:i A", $timeSlot);

        // Correctly map the day of the week
        $dayOfWeek = $this->mapDayOfWeek($dayOfWeek);

        // Check doctor's schedule
        $doctorSchedule = $doctor
            ->doctorSchedule()
            ->where("medical_center_id", $medicalCenter->id)
            ->where("day_of_week", $dayOfWeek)
            ->first();

        if (!$doctorSchedule) {
            return false; // Doctor doesn't work on this day at this medical center
        }

        $doctorStartTime = Carbon::parse($doctorSchedule->start_time);
        $doctorEndTime = Carbon::parse($doctorSchedule->end_time)->subMinutes(15);

        // Check if the time slot is within the doctor's working hours
        return $timeSlot->between($doctorStartTime, $doctorEndTime);
    }


    public function updatedSelectedDate($date)
    {
        $this->selectDate($date);
    }

    private function mapDayOfWeek($carbonDayOfWeek)
    {
        // Assuming your database uses 1 for Monday and 7 for Sunday
        return $carbonDayOfWeek === 0 ? 7 : $carbonDayOfWeek;
    }

    public function changeShowAddEditAppointmentModal(
        $appointment
    ) {
        if ($appointment) {
            $this->selectedAppointment = Appointment::find($appointment["id"]);
            $this->form->setMainData($this->selectedAppointment);
        }
    }

    public function hideAddEditAppointmentModal()
    {
        $this->selectedAppointment = null;
        $this->dispatch('close-modals');
    }

    public function save(): void
    {
        // $this->authorize('create', PatientRecord::class);
        $this->form->store();

        // Dispatch event to update appointments
        event(new AppointmentUpdated($this->selectedAppointment));

        $this->hideAddEditAppointmentModal();
        $this->success(
            "<u>{$this->successMessage}</u>",
            position: 'bottom-end',
            icon: 'c-check',
            css: 'bg-primary text-white'
        );

        // Refresh appointments
        $this->loadAppointments();
        $this->generateAppointments();
    }

    // public function getTreatents() {
    //     return $this->selectedAppointment->treatments;
    // }

    public function getPrescriptions()
    {
        return Cache::remember('prescriptions', 60 * 60, function () {
            return PrescriptionTemplate::all();
        });
    }

    public function updated($property, $value)
    {
        if ($property == 'form.prescription_template_id') {
            $this->showApproveOrCancelAdd = $value > 0 ? true : false;
            // $this->form->description = $value ? $this->prescriptions->find($value)?->content : '';
        }
    }

    public function addPrescriptionText()
    {
        $value = $this->form->prescription_template_id;
        $this->form->description = $this->form->description . ' ' . ($value ? $this->prescriptions->find($value)?->content : '');
        $this->hideApproveOrCancelAdd();
    }

    public function hideApproveOrCancelAdd()
    {
        $this->showApproveOrCancelAdd = false;
        $this->form->prescription_template_id = 0;
    }
}
