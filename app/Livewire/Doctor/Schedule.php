<?php

namespace App\Livewire\Doctor;

use App\Models\MedicalCenter;
use App\Models\User;
use Livewire\Component;
use Mary\Traits\Toast;

class Schedule extends Component
{
    use Toast;
    public $user;
    public $name;
    public $email;
    public $phone;
    public $username;
    public $userMedicalCenters;
    public $centerTimes;
    public $day;
    public $min;
    public $max;
    public $selectedDay = [];
    public $start = [];
    public $end = [];
    public $workingHours = [];

    public function mount()
    {
        $this->user = User::findOrFail(request()->id);
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->phone = $this->user->phone;
        $this->username = $this->user->username;
        if ($this->user->medicalCenters()->count() > 0) {
            $this->userMedicalCenters = $this->user->medicalCenters
                ->map(function ($medicalCenter) {
                    return $medicalCenter;
                });
        }
    }

    public function chooseCenter($id)
    {
        $this->centerTimes = MedicalCenter::with('workingHours')->findOrFail($id);
        $this->workingHours = $this->centerTimes->workingHours->map(function ($workingHour) {
            return [
                'opening_time' => substr($workingHour->opening_time, 0, 5),
                'closing_time' => substr($workingHour->closing_time, 0, 5),
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.doctor.schedule')->layout('layouts.dash');
    }
}
