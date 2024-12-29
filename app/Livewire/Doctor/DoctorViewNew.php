<?php

namespace App\Livewire\Doctor;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\WorkingHour;
use App\Models\MedicalCenter;
use App\Models\DoctorSchedule;
use App\Livewire\Forms\DrWorkingHoursForm;

class DoctorViewNew extends Component
{
    public DrWorkingHoursForm $scheduleForm;

    public array $tabs = [
        'personal_info' => [
            'title' => 'Personal Info',
            'icon' => '              <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                                <path d="M3 3v5h5"/>
                                <path d="M12 7v5l4 2"/>',
        ],
        'working_hours' => [
            'title' => 'Working Hours',
            'icon' => '   <path d="M20 7h-3a2 2 0 0 1-2-2V2"/>
                            <path d="M9 18a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h7l4 4v10a2 2 0 0 1-2 2Z"/>
                            <path d="M3 7.6v12.8A1.6 1.6 0 0 0 4.6 22h9.8"/>',
        ],
        'appointments' => [
            'title' => 'Appointments',
            'icon' => '<path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/>',
        ],
        // 'patient ' => [
        //     'title' => 'Patient',
        //     'icon' => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        // ],
    ];

    public array $workingHoursHeaders = [];
    public string $activeTab = 'personal_info';
    public $doctor;

    public ?MedicalCenter $selectedMedicalCenter = null;

    public bool $showEditWorkingHourModal = false;
    public bool $showAddDayToScheduleModal = false;

    public ?DoctorSchedule $selectedSchedule = null;
    public ?WorkingHour $selectedMedicalCenterWorkingHour = null;

    public function mount(int $id)
    {
        // if currentuser hase role doctor set activetab to personal info
        if (auth()->user()->hasRole('doctor')) {

            $this->setActiveTab('appointments');
        } else {
            $this->setActiveTab('personal_info');
        }
        $this->loadDoctorData($id);
        if ($this->doctor->medicalCenters->count() > 0) {
            $this->selectedMedicalCenter = $this->doctor->medicalCenters->first();
        }
        $this->workingHoursHeaders = [['key' => 'day_of_week', 'label' => __('Day')], ['key' => 'start_time', 'label' => __('Start')], ['key' => 'end_time', 'label' => __('End')], ['key' => 'actions', 'label' => __('Actions')]];
    }

    public function render()
    {
        return view('livewire.doctor.doctor-view-new')->layout('layouts.dash');
    }

    public function loadDoctorData(int $id)
    {
        $doctor = User::where('id', $id)
            ->whereHas('role', function ($query) {
                $query->where('name', 'doctor');
            })
            ->firstOrFail();

        $this->authorize('view', $doctor);
        $this->doctor = $doctor;
    }

    public function setActiveTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    public function setMedicalCenter($medicalCenter)
    {
        $this->selectedMedicalCenter = null;
        if (is_array($medicalCenter)) {
            $medicalCenter = MedicalCenter::find($medicalCenter['id']);
        }
        $this->selectedMedicalCenter = $medicalCenter;
        //        $this->scheduleForm->setData($this->form->user->doctorSchedule->where('medical_center_id',
        //            $medicalCenter->id)->toArray());
    }

    public function getDoctorMedicalCenterSchedule()
    {
        return $this->doctor
            ->doctorSchedule()
            ->where('medical_center_id', $this->selectedMedicalCenter->id)
            ->orderBy('day_of_week')
            ->get();
    }

    public function getRowDecoration(): array
    {
        return [
            'hover:!bg-accent' => fn($role) => true,
        ];
    }

    public function deleteWorkingHour($id)
    {
        $workingHour = WorkingHour::find($id);
        $this->authorize('delete', $workingHour);
        $workingHour->delete();
    }

    public function editWorkingHour($id)
    {
        $workingHour = DoctorSchedule::find($id);
        $this->changeWorkingHourModalState(true);
        $this->selectedSchedule = $workingHour;
        $this->selectedMedicalCenterWorkingHour = WorkingHour::where('day_of_week', $workingHour->day_of_week)
            ->where('medical_center_id', $this->selectedMedicalCenter->id)
            ->first();
        $this->scheduleForm->setDoctorSchedule($workingHour);
        $this->scheduleForm->setMinMaxTime($this->selectedMedicalCenterWorkingHour->opening_time, $this->selectedMedicalCenterWorkingHour->closing_time);
    }

    public function changeWorkingHourModalState(bool $state)
    {
        if (!$state) {
            $this->selectedSchedule = null;
            $this->selectedMedicalCenterWorkingHour = null;
            $this->scheduleForm->reset();
        }
        $this->showEditWorkingHourModal = $state;
    }

    public function addNewWorkingHour($day_of_week)
    {
        $this->scheduleForm->setDoctorMedicalCenter($this->doctor->id, $this->selectedMedicalCenter->id, $day_of_week);

        $this->selectedMedicalCenterWorkingHour = WorkingHour::where('day_of_week', $day_of_week)
            ->where('medical_center_id', $this->selectedMedicalCenter->id)
            ->first();
        $this->scheduleForm->setMinMaxTime($this->selectedMedicalCenterWorkingHour->opening_time, $this->selectedMedicalCenterWorkingHour->closing_time);
        $this->changeAddDayToScheduleModalState(false);
        $this->changeWorkingHourModalState(true);
    }

    public function changeAddDayToScheduleModalState(bool $state)
    {
        $this->showAddDayToScheduleModal = $state;
    }

    public function saveWorkingHours()
    {
        if ($this->selectedSchedule) {
            $this->authorize('update', $this->selectedSchedule);
            $this->scheduleForm->update();
        } else {
            $this->authorize('create', DoctorSchedule::class);
            $this->scheduleForm->store();
        }
        $this->changeWorkingHourModalState(false);
    }

    public function formatTime($time)
    {
        return $formattedTime = Carbon::createFromFormat('H:i:s', $time)->format('h:i A');
    }

    public function availableDaysToAddToSchedule()
    {
        $days = array_diff($this->selectedMedicalCenter->workingHours()->pluck('day_of_week')->toArray(), $this->getDoctorMedicalCenterSchedule()->pluck('day_of_week')->toArray());
        return $days;
    }

    ///
}
