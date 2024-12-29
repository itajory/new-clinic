<?php

namespace App\Livewire\Appointment;

use Mary\Traits\Toast;
use Livewire\Component;
use App\Models\Appointment;
use App\Livewire\Forms\AppointmentForm;

class ViewAppointment extends Component
{
    use Toast;

    public AppointmentForm $form;
    public $selectedPatient;
    public $selectedDoctor;
    public $selectedMedicalCenter;
    public $selectedTreatment;
    public $selectedAppointment;
    public $patientFund;
    public string $successMessage = '';

    public function mount(
        $selectedAppointment,
        $selectedPatient,
        $selectedDoctor,
        $selectedMedicalCenter,
        $selectedTreatment
    ): void {
        $this->selectedAppointment = $selectedAppointment;
        $this->selectedPatient = $selectedPatient;
        $this->selectedDoctor = $selectedDoctor;
        $this->selectedMedicalCenter = $selectedMedicalCenter;
        $this->selectedTreatment = $selectedTreatment;
        $this->patientFund = $selectedAppointment->patientFund;

        $this->form->setAppointmetnForUpdate($selectedAppointment);
        $this->successMessage = __('success');
    }

    public function render()
    {
        return view('livewire.appointment.view-appointment');
    }

    public function updated($property, $value): void
    {
        if ($property == 'form.status') {
            $this->form->changeStatus($value);
        }
        if ($property == 'form.isCost') {
            $this->form->chnageIsCost($value);
        }

        if ($property == 'form.patient_fund_amount') {
            $this->form->setPatientFundTotal();
        }

        if (in_array(
            $property,
            ['form.patient_fund_amount', 'form.discount', 'form.price']
        )) {
            $this->form->setTotal();
        }
    }

    public function updateStatus()
    {
        $this->authorize('update', $this->selectedAppointment);
        $this->form->updateStatus();
        $this->success(
            "<u>{$this->successMessage}</u>",
            position: 'bottom-end',
            icon: 'c-check',
            css: 'bg-primary text-white'
        );
        $this->dispatch('hide-add-edit-appointment-modal');
//        $this->reset();
    }
}
