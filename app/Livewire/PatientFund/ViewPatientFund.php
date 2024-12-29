<?php

namespace App\Livewire\PatientFund;

use Mary\Traits\Toast;
use Livewire\Component;
use App\Models\PatientFund;
use Livewire\Attributes\On;

class ViewPatientFund extends Component
{
    use Toast;
    public $patientFund;
    public $appointments = [];
    public $headers; // this will be sent to child table
    public $parentView = 'patient_fund'; // this will be sent to child table

    public array $tabs = [
        'not_closed',
        'closed',
    ];
    public string $activeTab = '';

    public $selectedAppointments = [];



    public function mount(int $id): void
    {
        $this->activeTab = 'not_closed';
        $this->loadPatientFundData($id);
        $this->getNotClosedAppointments();
        $this->headers = [
            ['key' => 'id', 'label' => trans('ID')],
            ['key' => 'appointment_time', 'label' => trans('Date')],
            ['key' => 'status', 'label' => trans('Status')],
            ['key' => 'price', 'label' => trans('Price')],
            ['key' => 'discount', 'label' => trans('Discount')],
            ['key' => 'patient_fund_id', 'label' => trans('Paient Fund')],
            ['key' => 'patient_fund_total', 'label' => trans('Paient Fund Total')],
            // ['key' => 'total', 'label' => trans('Total')],
        ];
    }


    public function render()
    {
        return view('livewire.patient-fund.view-patient-fund')->layout('layouts.dash');
    }

    public function loadPatientFundData(int $id)
    {
        $patientFund = PatientFund::findOrFail($id);
        $this->authorize('view', $patientFund);
        $this->patientFund = $patientFund;
    }



    public function setTab(string $tab): void
    {
        // $this->totalSelected = 0;
        $this->activeTab = $tab;
        // $this->clearFilters();
        match ($tab) {
            'not_closed' => $this->getNotClosedAppointments(),
            'closed' => $this->getClosedAppointments(),
        };
    }


    public function getNotClosedAppointments()
    {
        $this->appointments = $this->patientFund->appointments()
            ->with([
                'patient',
                'medicalCenter',
                'createdBy'
            ])
            ->where('is_patient_fund_closed', false)
            ->whereIn('status', ['completed', 'not_attended_with_telling', 'not_attended_without_telling'])
            ->get();

        // dd($this->appointments);
    }

    public function getClosedAppointments()
    {
        $this->appointments = $this->patientFund->appointments()
            ->with([
                'patient',
                'medicalCenter',
                'createdBy'
            ])
            ->where('is_patient_fund_closed', true)
            ->whereIn('status', ['completed', 'not_attended_with_telling', 'not_attended_without_telling'])
            ->get();
    }


    #[On('selectedRowsUpdated')]
    public function setSelectedAppointments($selected)
    {
        $this->selectedAppointments = $selected;
    }

    public function closeAppointments()
    {
        $appointments = $this->patientFund->appointments()->whereIn('id', $this->selectedAppointments)->get();

        foreach ($appointments as $appointment) {
            $appointment->is_patient_fund_closed = true;
            $appointment->save();
        }
        $this->setTab('closed');
    }
}
