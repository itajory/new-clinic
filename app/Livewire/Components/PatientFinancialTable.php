<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Queue\NullQueue;
use Illuminate\Database\Eloquent\Collection;

class PatientFinancialTable extends Component
{
    public array $headers;
    public  $appointments = null;
    public  $selectedRows = [];
    public $selectAll = false;
    public $activeTab = '';

    public $viewAppointment = null;

    public $showAppointmentModal = false;
    public $parentView = '';

    public function mount($appointments, string $activeTab, $headers = null, $parentView = 'patient'): void
    {
        $this->parentView = $parentView;
        $this->appointments = $appointments;
        $this->activeTab = $activeTab;
        if ($headers == null) {
            $this->headers = [
                ['key' => 'id', 'label' => trans('ID')],
                ['key' => 'appointment_time', 'label' => trans('Date')],
                ['key' => 'status', 'label' => trans('Status')],
                ['key' => 'price', 'label' => trans('Price')],
                ['key' => 'discount', 'label' => trans('Discount')],
                ['key' => 'patient_fund_id', 'label' => trans('Paient Fund')],
                ['key' => 'patient_fund_total', 'label' => trans('Paient Fund Total')],
                ['key' => 'total', 'label' => trans('Total')],
            ];
            $this->activeTab == 'paid' ?  $this->headers[] = ['key' => 'payments', 'label' => trans('Payment')] : '';
            $this->headers[] =   ['key' => 'actions', 'label' => trans('Actions')];
        } else {
            $this->headers =  $headers;
        }
    }

    public function render()
    {
        return view('livewire.components.patient-financial-table');
    }


    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedRows = $this->appointments->pluck('id')->toArray();
        } else {
            $this->selectedRows = [];
        }
        $this->dispatch('selectedRowsUpdated', $this->selectedRows);
    }

    public function updatedSelectedRows()
    {
        $this->selectAll = count($this->selectedRows) === count($this->appointments);
        $this->dispatch('selectedRowsUpdated', $this->selectedRows);
    }

    public function showAppointment($appointment = null)
    {
        $this->viewAppointment = $appointment;
        if ($appointment == null) {
            $this->showAppointmentModal = false;
        } else {

            $this->showAppointmentModal = true;
        }
    }
}
