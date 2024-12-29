<?php

namespace App\Livewire\Doctor;

use App\Livewire\Forms\DoctorScheduleForm;
use App\Livewire\Forms\UserForm;
use App\Models\MedicalCenter;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Mary\Traits\Toast;

class DoctorView extends Component
{
    use Toast;

    public UserForm $form;
    public DoctorScheduleForm $scheduleForm;
    public $doctor;
    public bool $editMode = false;
    public $id;
    public Collection $medicalCenters;
    public ?MedicalCenter $selectedMedicalCenter = null;

    public function mount(int $id)
    {
        $this->id = $id;
        $this->loadDoctorData();
//        $this->scheduleForm->setData($doctor->doctorSchedule->toArray());
//        $this->medicalCenters = MedicalCenter::all();

    }

    private function loadDoctorData()
    {
        $doctor = User::where('id', $this->id)->firstOrFail();
        $this->authorize('view', $doctor);
        $this->form->setUser($doctor);
        $this->doctor = $doctor;
    }

    public function setEditMode($value)
    {
        $this->editMode = $value;
        $this->form->setUser($this->doctor);
    }

    public function save(): void
    {
        if ($this->editMode) {
            $this->authorize('update', $this->form->user);
            $this->form->update();
            $this->editMode = false;
        }
//        else {
//            $this->authorize('create', User::class);
//            $this->form->store();
//        }
        $this->success(
            "<u>{$this->form->user->name}</u> has been saved successfully",
            position: 'bottom-end',
            icon: 'c-check',
            css: 'bg-primary text-white'
        );
    }

//    public function setMedicalCenter($medicalCenterId)
//    {
//        dd('setMedicalCenter called with ID: ' . $medicalCenterId);
//        $this->scheduleForm->setData($this->form->user->doctorSchedule->where('medical_center_id',
//            $medicalCenterId)->toArray());
//    }

    public function setMedicalCenter($medicalCenter)
    {
        $this->scheduleForm->reset();
        $this->selectedMedicalCenter = null;
        if (is_array($medicalCenter)) {
            $medicalCenter = MedicalCenter::find($medicalCenter['id']);
        }
        $this->selectedMedicalCenter = $medicalCenter;
//        $this->scheduleForm->setData($this->form->user->doctorSchedule->where('medical_center_id',
//            $medicalCenter->id)->toArray());
    }

    public function render()
    {
        return view('livewire.doctor.doctor-view')->layout('layouts.dash');
    }

//    public function checkMCWorkingHours($medicalCenter, $dayOfWeek)
//    {
//        $day = $medicalCenter->workingHours->where('day_of_week', $dayOfWeek)->first();
//        $this->scheduleForm->setWorkingHours($day);
//        return $day;
//    }
    public function checkMCWorkingHours($dayOfWeek)
    {
        if (!$this->selectedMedicalCenter) {
            return null;
        }
        if (!$this->selectedMedicalCenter->relationLoaded('workingHours')) {
            $this->selectedMedicalCenter->load('workingHours');
        }

        $day = $this->selectedMedicalCenter->workingHours->where('day_of_week', $dayOfWeek)->first();
        $this->scheduleForm->setWorkingHours($day);
        return $day;
    }
}
