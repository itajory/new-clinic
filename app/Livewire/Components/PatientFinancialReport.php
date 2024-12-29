<?php

namespace App\Livewire\Components;

use Carbon\Carbon;
use Mary\Traits\Toast;
use Livewire\Component;
use Livewire\Attributes\On;

class PatientFinancialReport extends Component
{
    use Toast;
    public $patient;
    public $appointments;
    public $totalSelected;
    public $appointment_ids = [];
    public $successMessage = '';
    public $successMessageCheckUpdated = '';
    public $checks;
    public $selectedCheck = null;
    public int $filtersCount = 0;

    public array $tabs = [
        'not_paid',
        'paid',
        'appointments',
        'checks'
    ];
    public string $activeTab = '';

    public bool $showpaymentModal = false;
    public bool $showCheckModal = false;

    public  $dateFrom = null;
    public  $dateTo = null;
    public  $checkStatus = '';



    public function mount($patient): void
    {
        $this->patient = $patient;
        $this->activeTab = 'not_paid';
        $this->getNotPaidAppointment();
        $this->successMessage = trans('Payment recorded successfully');
        $this->successMessageCheckUpdated = trans('Check updated successfully');
    }

    public function render()
    {
        return view('livewire.components.patient-financial-report');
    }
    public function setTab(string $tab): void
    {
        $this->totalSelected = 0;
        $this->activeTab = $tab;
        // swith tab view & get aprropriate data
        $this->clearFilters();
        match ($tab) {
            'not_paid' => $this->getNotPaidAppointment(),
            'paid' => $this->getPaidAppointment(),
            'appointments' => $this->getPatientAppointments(),
            'checks' => $this->getChecks(),
        };
    }


    // public function getNotPaidAppointment($dateFrom = null, $dateTo = null): void
    // {
    //     $this->appointments = $this->patient->appointments()->with(['doctor', 'treatment', 'medicalCenter', 'payments'])->whereIn('status', ['completed', 'not_attended_with_telling', 'not_attended_without_telling'])->where('total', '>', 0)->whereDoesntHave('payments')
    //         ->when($dateFrom != null && $dateTo != null, function ($query) use ($dateFrom, $dateTo) {
    //             $query->whereBetween('appointment_time', [$dateFrom, $dateTo]);
    //         })
    //         ->orderBy('appointment_time', 'desc')->get();
    // }

    public function getNotPaidAppointment($dateFrom = null, $dateTo = null): void
    {
        $query = $this->patient->appointments()
            ->with(['doctor', 'medicalCenter', 'createdBy']) // Only load necessary relationships
            ->notPaidAndCompleted()
            ->when($dateFrom ?? $dateTo, function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('appointment_time', [
                    Carbon::parse($dateFrom),
                    Carbon::parse($dateTo)->endOfDay()
                ]);
            })
            ->orderBy('appointment_time', 'desc');

        $this->appointments = $query->get();
    }

    public function getPaidAppointment($dateFrom = null, $dateTo = null): void
    {

        $this->appointments = $this->patient->appointments()
            ->with(['doctor', 'treatment', 'medicalCenter', 'payments', 'createdBy'])
            ->whereIn('status', ['completed', 'not_attended_with_telling', 'not_attended_without_telling'])
            ->where(function ($query) {
                $query->where('total', '=', 0)
                    ->orWhereHas('payments');
            })
            ->when($dateFrom ?? $dateTo, function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('appointment_time', [
                    Carbon::parse($dateFrom),
                    Carbon::parse($dateTo)->endOfDay()
                ]);
            })->orderBy('appointment_time', 'desc')->get();
    }

    public function getPatientAppointments($dateFrom = null, $dateTo = null): void
    {
        $this->appointments = $this->patient->appointments()
            ->with(['doctor', 'treatment', 'medicalCenter', 'payments', 'createdBy'])
            ->when($dateFrom ?? $dateTo, function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('appointment_time', [
                    Carbon::parse($dateFrom),
                    Carbon::parse($dateTo)->endOfDay()
                ]);
            })
            ->orderBy('appointment_time', 'desc')
            ->get();
    }

    public function getChecks($dateFrom = null, $dateTo = null, $checkStatus = null): void
    {

        $this->checks = $this->patient->checks()->when($checkStatus != null, function ($query) use ($checkStatus) {
            $query->where('status', $checkStatus);
        })
            ->when($dateFrom != null && $dateTo != null, function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('date', [
                    Carbon::parse($dateFrom),
                    Carbon::parse($dateTo)->endOfDay()
                ]);
            })->orderBy('date', 'desc')->get();
    }

    #[On('filterFinances')]
    public function handleFinanceFilterChanges($dateFrom = null, $dateTo = null, $checkStatus = null, $filtersCount = 0): void
    {
        // dd($dateFrom, $dateTo, $checkStatus);
        $this->filtersCount = $filtersCount;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->checkStatus = $checkStatus;

        match ($this->activeTab) {
            'not_paid' => $this->getNotPaidAppointment(dateFrom: $dateFrom, dateTo: $dateTo),
            'paid' => $this->getPaidAppointment(dateFrom: $dateFrom, dateTo: $dateTo),
            'appointments' => $this->getPatientAppointments(dateFrom: $dateFrom, dateTo: $dateTo),
            'checks' => $this->getChecks(dateFrom: $dateFrom, dateTo: $dateTo, checkStatus: $checkStatus),
        };
    }


    #[On("selectedRowsUpdated")]
    public function getSelectedRowsSum($value)
    {
        $this->appointment_ids = $value;
        $this->showpaymentModal = false;
        $this->totalSelected =  $this->appointments->whereIn('id', $value)->sum('total');
    }

    #[On('hide-payment-modal')]
    public function chanegShowMpaymentModal(bool $show = false): void
    {

        $this->showpaymentModal =  $show;
    }


    #[On('paymentRecorded')]
    public function paymentRecorded()
    {
        $this->success(
            "<u>{$this->successMessage}</u>",
            position: 'bottom-end',
            icon: 'c-check',
            css: 'bg-primary text-white'
        );
        $this->getNotPaidAppointment();
        $this->showpaymentModal = false;
        $this->totalSelected = 0;
        $this->appointment_ids = [];
    }


    #[On('showCheckModal')]
    public function changeShowChecKModal(bool $show, $item = null)
    {
        if ($item != null) {

            $this->selectedCheck = $item;
        }

        $this->showCheckModal = $show;
        if ($show == false) {
            $this->selectedCheck = null;
        }
    }

    #[On('checkUpdated')]
    public function checkUpdated()
    {
        $this->getChecks();
        $this->success(
            title: "<u>{$this->successMessageCheckUpdated}</u>",
            position: 'bottom-end',
            icon: 'c-check',
            css: 'bg-primary text-white'
        );
        $this->showCheckModal = false;
    }

    public function showFinanceFilterDrawer()
    {
        $this->dispatch('showFinanceFilterDrawer', $this->dateFrom, $this->dateTo, $this->checkStatus);
    }

    public function clearFilters()
    {
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->checkStatus = '';
        $this->filtersCount = 0;
    }
}
