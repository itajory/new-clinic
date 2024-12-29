<?php

namespace App\Livewire\Components;

use App\Models\Bank;
use App\Models\Payment;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Livewire\Forms\PaymentForm;

class PaymentModal extends Component
{
    use WithFileUploads;

    public PaymentForm $form;
    public bool $showpaymentModal = false;
    public  $totalSelected = 0;
    public $patient_id;
    public $appointment_ids;

    public function mount($showpaymentModal, $totalSelected, $patient_id, $appointment_ids)
    {
        $this->showpaymentModal = $showpaymentModal;
        $this->totalSelected = $totalSelected;
        $this->patient_id = $patient_id;
        $this->appointment_ids = $appointment_ids;
        $this->form->amount = $this->totalSelected;
        $this->form->patient_id = $this->patient_id;
        $this->form->appointment_ids = $this->appointment_ids;
        $this->form->banks = Bank::all();
    }


    public function render()
    {
        return view('livewire.components.payment-modal');
    }

    public function hideModal(): void
    {
        $this->showpaymentModal = false;
        $this->dispatch('hide-payment-modal');
    }

    public function updated($property): void
    {
        if ($property === 'form.checksCount') {
            $this->form->setCheckFields();
        }
    }

    public function save()
    {
        $this->authorize('create', Payment::class);
        $this->form->store();
        $this->dispatch('paymentRecorded');
    }
}
