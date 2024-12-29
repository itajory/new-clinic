<?php

namespace App\Livewire\Forms;

use App\Models\DoctorSchedule;
use Carbon\Carbon;
use Livewire\Attributes\Validate;
use Livewire\Form;

class DrWorkingHoursForm extends Form
{
    public ?DoctorSchedule $doctorSchedule = null;
    public string $day_of_week = '';
    public string $start_time = '';
    public string $end_time = '';
    public int $user_id = 0;
    public int $medical_center_id = 0;
    public string $startTimeMinMax = '';
    public string $endTimeMinMax = '';
    public string $fromToHint = '';

    public function rules(): array
    {
        return [
            'day_of_week' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i:s|' . $this->startTimeMinMax,
            'end_time' => 'required|date_format:H:i:s|after:start_time|' . $this->endTimeMinMax,
            'user_id' => 'required|integer|exists:users,id',
            'medical_center_id' => 'required|integer|exists:medical_centers,id',
        ];
    }

    public function store(): void
    {
        $this->fixTimeFormat();

        $this->validate();
        DoctorSchedule::create([
            'day_of_week' => $this->day_of_week,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'user_id' => $this->user_id,
            'medical_center_id' => $this->medical_center_id,
        ]);

    }

    public function fixTimeFormat(): void
    {
        $this->start_time = strlen($this->start_time) == 8 ? $this->start_time : $this->start_time . ':00';
        $this->end_time = strlen($this->end_time) == 8 ? $this->end_time : $this->end_time . ':00';
    }

    public function update(): void
    {

        $this->fixTimeFormat();


        $this->validate();
        $this->doctorSchedule->update([
            'day_of_week' => $this->day_of_week,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'user_id' => $this->user_id,
            'medical_center_id' => $this->medical_center_id,
        ]);
    }

    public function setDoctorSchedule(DoctorSchedule $doctorSchedule): void
    {
        $this->doctorSchedule = $doctorSchedule;
        $this->day_of_week = $doctorSchedule->day_of_week;
        $this->start_time = $doctorSchedule->start_time;
        $this->end_time = $doctorSchedule->end_time;
        $this->user_id = $doctorSchedule->user_id;
        $this->medical_center_id = $doctorSchedule->medical_center_id;
    }

    public function setMinMaxTime(string $startTime, string $endTime): void
    {
        $this->fromToHint = trans('From') . ' ' . $this->formatTime($startTime) . ' ' . trans('to') . ' ' . $this->formatTime($endTime);
        $this->startTimeMinMax = 'after_or_equal:' . $startTime . '|before_or_equal:' . $endTime;
        $this->endTimeMinMax = 'before_or_equal:' . $endTime;
    }

    public function formatTime($time)
    {
        return $formattedTime = Carbon::createFromFormat('H:i:s', $time)->format('h:i A');
    }

    public function setDoctorMedicalCenter(int $doctorId, int $medicalCenterId, $day_of_week): void
    {
        $this->day_of_week = $day_of_week;
        $this->user_id = $doctorId;
        $this->medical_center_id = $medicalCenterId;
    }

}
