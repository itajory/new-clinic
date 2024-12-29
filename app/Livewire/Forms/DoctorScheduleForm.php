<?php

namespace App\Livewire\Forms;

use App\Models\DoctorSchedule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class DoctorScheduleForm extends Form
{
    public ?DoctorSchedule $doctorSchedule = null;
    public int $user_id = 0;
    public int $medical_center_id = 0;

    public array $selectedWorkingHours = [
        [
            'day_of_week' => 1,
            'start_time' => '00:00',
            'min' => '00:00',
            'end_time' => '00:00',
            'max' => '00:00',
            'selected' => false,
            'can_select' => true
        ],
        [
            'day_of_week' => 2,
            'start_time' => '00:00',
            'min' => '00:00',
            'end_time' => '00:00',
            'max' => '00:00',
            'selected' => false,
            'can_select' => true
        ],
        [
            'day_of_week' => 3,
            'start_time' => '00:00',
            'min' => '00:00',
            'end_time' => '00:00',
            'max' => '00:00',
            'selected' => false,
            'can_select' => true
        ],
        [
            'day_of_week' => 4,
            'start_time' => '00:00',
            'min' => '00:00',
            'end_time' => '00:00',
            'max' => '00:00',
            'selected' => false,
            'can_select' => true
        ],
        [
            'day_of_week' => 5,
            'start_time' => '00:00',
            'min' => '00:00',
            'end_time' => '00:00',
            'max' => '00:00',
            'selected' => false,
            'can_select' => true
        ],
        [
            'day_of_week' => 6,
            'start_time' => '00:00',
            'min' => '00:00',
            'end_time' => '00:00',
            'max' => '00:00',
            'selected' => false,
            'can_select' => true
        ],
        [
            'day_of_week' => 7,
            'start_time' => '00:00',
            'min' => '00:00',
            'end_time' => '00:00',
            'max' => '00:00',
            'selected' => false,
            'can_select' => true
        ],
    ];

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer',
            'medical_center_id' => 'required|integer',
            'selectedWorkingHours.*.day_of_week' => 'required',
            'selectedWorkingHours.*.start_time' => 'required',
            'selectedWorkingHours.*.end_time' => 'required',
        ];
    }

    public function setData(array $selectedWorkingHours2): void
    {
        if (empty($selectedWorkingHours2)) {
            return;
        }
        $this->user_id = $selectedWorkingHours2[0]->user_id;
        $this->medical_center_id = $selectedWorkingHours2[0]->medical_center_id;
        $this->selectedWorkingHours = $selectedWorkingHours2[0]->workingHours->toArray();
        $this->selectedWorkingHours = array_map(function ($workingHour) use ($selectedWorkingHours2) {
            foreach ($selectedWorkingHours2 as $workingHour2) {
                if ($workingHour['day_of_week'] === $workingHour2['day_of_week']) {
                    $workingHour['start_time'] = $workingHour2['start_time'];
                    $workingHour['end_time'] = $workingHour2['end_time'];
                    $workingHour['selected'] = true;
                }
            }
            return $workingHour;
        }, $this->selectedWorkingHours);
    }

    public function setWorkingHours($day)
    {

        if (empty($day)) {
            return;
        }
        $this->selectedWorkingHours = array_map(function ($workingHour) use ($day) {
            if ($workingHour['day_of_week'] === $day->day_of_week) {
                $workingHour['min'] = $day->opening_time;
                $workingHour['start_time'] = $day->opening_time;
                $workingHour['max'] = $day->closing_time;
                $workingHour['end_time'] = $day->closing_time;
//                $workingHour['selected'] = false;
                $workingHour['can_select'] = true;
            } else {
                $workingHour['min'] = "00:00";
                $workingHour['start_time'] = "00:00";
                $workingHour['max'] = "00:00";
                $workingHour['end_time'] = "00:00";
//                $workingHour['selected'] = false;
                $workingHour['can_select'] = false;
            }
            return $workingHour;
        }, $this->selectedWorkingHours);

    }


}
