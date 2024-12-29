<?php

namespace App\Livewire\Appointment;

use App\Models\User;
use Mary\Traits\Toast;
use Livewire\Component;
use App\Models\Appointment;
use Livewire\Attributes\On;
use App\Livewire\Forms\AppointmentForm;
use Illuminate\Validation\ValidationException;

class CreateAppointment extends Component
{
    use Toast;
    public AppointmentForm $form;
    public  $selectedPatient;
    public  $selectedDoctor;
    public  $selectedMedicalCenter;
    public  $selectedTreatment;
    public  $durations;
    public $patientName;
    public $selectedTimeSlot;
    public $selectedDate;
    public string $successMessage = '';

    public function mount($selectedPatient, $selectedDoctor, $selectedMedicalCenter, $selectedTreatment, $durations, $selectedTimeSlot, $selectedDate)
    {
        $this->selectedPatient = $selectedPatient;
        $this->patientName = $selectedPatient->full_name;
        $this->selectedDoctor = $selectedDoctor;
        $this->selectedMedicalCenter = $selectedMedicalCenter;
        $this->selectedTreatment = $selectedTreatment;
        $this->durations = $durations;
        $this->selectedTimeSlot = $selectedTimeSlot;
        $this->selectedDate = $selectedDate;
        $this->form->setAppointmentMainInfo($this->selectedDoctor, $this->selectedMedicalCenter, $this->selectedTreatment, $this->selectedPatient, $this->selectedTimeSlot, $this->selectedDate);
        $this->form->patient_full_name = $selectedPatient->full_name;
        $this->initPatientFund();
        $this->successMessage = __('success');
    }

    public function render()
    {
        return view('livewire.appointment.create-appointment');
    }

    public function save()
    {
        $this->authorize('create', Appointment::class);
        $this->form->store();
        $this->success(
            "<u>{$this->successMessage}</u>",
            position: 'bottom-end',
            icon: 'c-check',
            css: 'bg-primary text-white'
        );
        $this->dispatch('hide-add-edit-appointment-modal');
    }

    public function updated($property, $value)
    {
        if ($property == 'form.patient_fund_id') {
            $this->form->patient_fund_contribution_type = $this->selectedPatient->patientFunds->find($value)->contribution_type;
            $this->form->patient_fund_amount = $this->selectedPatient->patientFunds->find($value)->pivot->contribution_percentage;
            $this->form->setPatientFundTotal();
            $this->form->setTotal();
        }
        if ($property == 'form.duration') {
            $this->form->setDateTo();
            $this->setRepeat($this->form->repeat);
        }
        if ($property == 'form.patient_fund_amount') {
            $this->form->setPatientFundTotal();
        }

        if (in_array(
            $property,
            ['form.patient_fund_amount', 'form.discount', 'form.price']
        )) {
            $this->form->setPatientFundTotal();
            $this->form->setTotal();
        }
    }

    public function initPatientFund()
    {
        if ($this->selectedPatient->patientFunds->count() > 0) {
            $this->form->patient_fund_id = $this->selectedPatient->patientFunds[0]->id;
            $this->form->patient_fund_contribution_type = $this->selectedPatient->patientFunds[0]->contribution_type;
            $this->form->patient_fund_amount = $this->selectedPatient->patientFunds[0]->pivot->contribution_percentage;
            $this->form->setPatientFundTotal();
            $this->form->setTotal();
        }
    }

    public function setRepeat($value)
    {
        try {
            $this->form->setRepeat($value);
        } catch (ValidationException $e) {
            $this->addError('repeat', $e->getMessage());
        }
    }
}
