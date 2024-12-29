<?php

namespace App\Livewire\Forms;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Form;

class AppointmentForm extends Form
{
    private const NUMERIC_RULE = 'required|numeric|min:0';

    public ?Appointment $appointment = null;
    public $patient_id;
    public $doctor_id;
    public $medical_center_id;
    public $treatment_id;
    public $appointment_time;
    public $duration;
    public $created_by = 0;
    public $status = 'reserved';
    public $note;
    public $repeat = 0;
    public $repeat_id;
    public $price;
    public $discount;
    public $patient_fund_id = null;
    public $patient_fund_amount = 0;
    public $patient_fund_total;
    public $total;
    public $patient_fund_contribution_type;
    public $patient_full_name;

    public $doctor;
    public $medicalCenter;
    public $treatment;
    public $patient;
    public $doctorName;
    public $medicalCenterName;
    public $treatmentName;
    public $patientName;
    public $dateTo;
    public $isCost = false;
    public bool $showIsCost = false;

    public $repeatArray = [
        [
            'id' => 0,
        ],
        [
            'id' => 1,
        ],
        [
            'id' => 2,
        ],
        [
            'id' => 3,
        ],
        [
            'id' => 4,
        ],
        [
            'id' => 5,
        ],
        [
            'id' => 6,
        ],
        [
            'id' => 7,
        ],
        [
            'id' => 8,
        ],
        [
            'id' => 9,
        ],
        [
            'id' => 10,
        ],
    ];

    public $allStatus = [
        [
            'id' => 'reserved',
        ],
        [
            'id' => 'waiting',
        ],
        // [
        //     "id" => "completed", // removed it from anyone it related to doctors when they create patient record
        // ],
        [
            'id' => 'not_attended_with_telling',
        ],
        [
            'id' => 'not_attended_without_telling',
        ],
    ];

    public function rules(): array
    {
        $r = [
            'patient_id' => 'required|integer|exists:patients,id',
            'doctor_id' => 'required|integer|exists:users,id',
            'medical_center_id' => 'required|integer|exists:medical_centers,id',
            'treatment_id' => 'required|integer|exists:treatments,id',
            'appointment_time' => 'required|date|after_or_equal:today',
            'duration' => 'required|integer',
            'created_by' => 'required|integer|exists:users,id',
            'status' => 'required|string|in:reserved,waiting,completed,not_attended_with_telling,not_attended_without_telling',
            'note' => 'nullable|string',
            'repeat' => 'required|integer|min:0',
            'repeat_id' => 'nullable|integer|exists:appointments,id',
            'price' => self::NUMERIC_RULE,
            'discount' => self::NUMERIC_RULE,
//            'patient_fund_id' => 'nullable|integer|exists:patient_funds,id',
            'total' => self::NUMERIC_RULE,
        ];

        if ($this->patient_fund_id == null) {
            $r['patient_fund_id'] = 'nullable';
            $r['patient_fund_amount'] = 'nullable';
            $r['patient_fund_total'] = 'nullable';
            $r['patient_fund_contribution_type'] = 'nullable';
        } else {
            $r['patient_fund_id'] = 'required|integer|exists:patient_funds,id';
            $r['patient_fund_amount'] = 'required|numeric|min:0';
            $r['patient_fund_total'] = 'required|numeric|min:0';
            $r['patient_fund_contribution_type'] = 'required|string|in:percentage,fixed';
        }

        return $r;
    }

    public function store()
    {
        $this->created_by = auth()->id();
        $this->validate();
        $this->checkConflictingAppointments();

        DB::transaction(function () {
            $this->appointment = Appointment::create([
                'patient_id' => $this->patient_id,
                'doctor_id' => $this->doctor_id,
                'medical_center_id' => $this->medical_center_id,
                'treatment_id' => $this->treatment_id,
                'appointment_time' => $this->appointment_time,
                'duration' => $this->duration,
                'created_by' => $this->created_by,
                'status' => $this->status,
                'note' => $this->note,
                'repeat' => $this->repeat,
                'repeat_id' => $this->repeat_id,
                'price' => $this->price,
                'discount' => $this->discount,
                'patient_fund_id' => $this->patient_fund_id,
                'patient_fund_amount' => $this->patient_fund_amount,
                'patient_fund_total' => $this->patient_fund_total,
                'total' => $this->total,
                'patient_fund_contribution_type' => $this->patient_fund_contribution_type,
            ]);

            if ($this->repeat > 0) {
                $startTime = Carbon::parse($this->appointment_time);
                for ($i = 1; $i <= $this->repeat; ++$i) {
                    $nextStartTime = $startTime->copy()->addWeeks($i);
                    $newAppointment = $this->appointment->replicate();
                    $newAppointment->repeat_id = $this->appointment->id;
                    $newAppointment->appointment_time = $nextStartTime;
                    $newAppointment->repeat = 0;
                    $newAppointment->save();
                }
            }
        });
    }

    public function setAppointmetnForUpdate($appointment)
    {
        $this->appointment = $appointment;
        $this->status = $appointment->status;
        $this->note = $appointment->note ?? '';
    }

    public function updateStatus()
    {
        DB::transaction(function () {
            $this->appointment->status = $this->status;
            $this->appointment->note = $this->note;
            $costStatuses = ['completed', 'not_attended_with_telling', 'not_attended_without_telling'];
            if (in_array($this->status, $costStatuses)) {
                if ($this->isCost) {
                    $validatedData = [
                        'price' => ['required', 'numeric', 'min:0'],
                        'discount' => ['required', 'numeric', 'max:100'],
//                        'patient_fund_id' => ['required', 'integer', 'exists:patient_funds,id'],
                        'patient_fund_amount' => ['required', 'numeric', 'min:0'],
                        'patient_fund_total' => ['required', 'numeric', 'min:0'],
                        'total' => ['required', 'numeric', 'gt:0'],
                    ];

                    $this->validate($validatedData);
                    $this->appointment->price = $this->price;
                    $this->appointment->discount = $this->discount;
                    $this->appointment->patient_fund_id = $this->patient_fund_id;
                    $this->appointment->patient_fund_amount = $this->patient_fund_amount;
                    $this->appointment->patient_fund_total = $this->patient_fund_total;
                    $this->appointment->total = $this->total;
                    // Invoice::create([
                    //     "appointment_id" => $this->appointment->id,
                    //     "price" => $this->price,
                    //     "discount" => $this->discount,
                    //     "patient_fund_id" => $this->patient_fund_id,
                    //     "patient_fund_amount" => $this->patient_fund_amount,
                    //     "total" => $this->total,
                    //     "created_by" => auth()->id(),
                    //     "status" => "pending",
                    //     "medical_center_id" => $this->appointment->medical_center_id,
                    //     "patient_id" => $this->appointment->patient_id,
                    //     "description" => $this->note
                    // ]);
                } else {
                    $this->appointment->price = 0;
                    $this->appointment->discount = 0;
                    $this->appointment->patient_fund_amount = 0;
                    $this->appointment->patient_fund_total = 0;
                    $this->appointment->total = 0;
                }
            }
            $this->appointment->save();
        });
    }

    public function changeStatus(string $status): void
    {
        $costStatuses = ['completed', 'not_attended_with_telling', 'not_attended_without_telling'];
        if (in_array($status, $costStatuses)) {
            $this->showIsCost = true;
            $this->chnageIsCost(true);
        } else {
            $this->showIsCost = false;
            $this->chnageIsCost(false);
        }
    }

    public function chnageIsCost($value): void
    {
        if ($value == true) {
            $this->price = $this->appointment->price;
            $this->discount = $this->appointment->discount;
            $this->patient_fund_id = $this->appointment->patient_fund_id ?? null;
            $this->patient_fund_amount = $this->appointment->patient_fund_amount ?? 0;
            $this->patient_fund_total = $this->appointment->patient_fund_total;
            $this->patient_fund_contribution_type = $this->appointment->patient_fund_contribution_type;
            $this->total = $this->appointment->total;
            $this->setPatientFundPercentage();
        }
    }

    public function setAppointmentMainInfo(
        $doctor,
        $medicalCenter,
        $treatment,
        $patient,
        $selectedTimeSlot,
        $selectedDate
    ) {
        // Set data for view form
        $this->doctor = $doctor;
        $this->doctorName = $doctor['name'];
        $this->medicalCenter = $medicalCenter;
//        $this->medicalCenterName = $medicalCenter->name;
        $this->medicalCenterName = $medicalCenter['name'];
        $this->treatment = $treatment;
        $this->treatmentName = $treatment->name;
        $this->patient = $patient;
        $this->patientName = $patient->full_name;

        // Set the appointment main info
        $this->patient_id = $patient->id;
//        $this->doctor_id = $doctor['id'];
        $this->doctor_id = $doctor['id'];
        $this->medical_center_id = $medicalCenter['id'];
        $this->treatment_id = $treatment->id;
        $this->price = $treatment->price;
        $this->duration = $treatment->duration;

        $this->appointment_time = Carbon::createFromFormat(
            'm/d/Y g:i A',
            $selectedDate . ' ' . $selectedTimeSlot
        );
        $this->setDateTo();
        $this->setPatientFundTotal();
        $this->setTotal();
    }

    public function setDateTo()
    {
        $this->dateTo = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $this->appointment_time
        )
            ->addMinutes($this->duration)
            ->format('Y-m-d H:i:s');
    }

    public function setPatientFundTotal()
    {
        if ($this->patient_fund_contribution_type == 'percentage') {
            $this->patient_fund_total =
                ($this->patient_fund_amount / 100) * ($this->price - $this->discount);
        } else {
            $this->patient_fund_total = $this->patient_fund_amount;
        }
    }

    public function setPatientFundPercentage()
    {
        if ($this->patient_fund_contribution_type == 'percentage') {
            $this->patient_fund_amount =
                ($this->patient_fund_total / ($this->price - $this->discount)) * 100;
        } else {
            $this->patient_fund_amount = $this->appointment->patient_fund_amount;
        }

        $this->setPatientFundTotal();
    }

    public function setTotal()
    {
        $this->discount = $this->discount ?? 0;
        $this->total =
            $this->price -
            $this->patient_fund_total -
            ($this->price * $this->discount) / 100;
    }

    public function setRepeat($value)
    {
        $this->repeat = $value;
        $this->checkConflictingAppointments();
    }

    // todo: fix it so it can check for conflicting appointments in appointment time + duration
    private function checkConflictingAppointments()
    {
        if ($this->repeat > 0) {
            $startTime = Carbon::parse($this->appointment_time);
            $endTime = $startTime->copy()->addMinutes($this->duration);

            for ($i = 1; $i <= $this->repeat; ++$i) {
                $nextStartTime = $startTime->copy()->addWeeks($i);
                $nextEndTime = $endTime->copy()->addWeeks($i);

                $conflictingAppointment = Appointment::where(
                    'doctor_id',
                    $this->doctor_id
                )
                    ->where(function ($query) use (
                        $nextStartTime,
                        $nextEndTime
                    ) {
                        $query
                            ->whereBetween('appointment_time', [
                                $nextStartTime,
                                $nextEndTime,
                            ])
                            ->orWhereBetween(
                                \DB::raw(
                                    'DATE_ADD(appointment_time, INTERVAL duration MINUTE)'
                                ),
                                [$nextStartTime, $nextEndTime]
                            );
                    })
                    ->first();

                if ($conflictingAppointment) {
                    throw ValidationException::withMessages(['repeat' => 'Conflicting appointment found on ' . $nextStartTime->format('Y-m-d H:i:s')]);
                }
            }
        }
    }
}
