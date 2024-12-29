<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Appointment;
use Livewire\Attributes\On;
use App\Models\PrintedInvoice;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintInvoice extends Component
{
    public $appointment_ids;
    public $appointments;
    public $patient;
    public $invoice;
    public function mount($appointment_ids = [], $patient = null)
    {
        $this->appointment_ids = $appointment_ids;
        $this->patient = $patient;
        $this->loadAppointments();
    }
    public function render()
    {

        return view(
            'livewire.components.print-invoice',
            data: ['appointment_ids' => $this->appointment_ids]
        );
    }

    public function loadAppointments()
    {
        $this->appointments = Appointment::whereIn('id',  $this->appointment_ids)
            ->with([
                'patientFund',
                'doctor',
                'medicalCenter',
                'treatment',
                'createdBy',

            ])->get();
    }
    public function printInvoices()
    {
        $this->invoice = PrintedInvoice::create([
            'appointment_ids' => $this->appointment_ids,
            'created_by' => auth()->user()->id
        ]);

        // create invoice
        $pdf = Pdf::loadView(
            'livewire.components.invoice-pdf',
            ['appointments' => $this->appointments, 'patient' => $this->patient, 'appointment_ids' => $this->appointment_ids, 'invoice' => $this->invoice]
        );
        $pdf->setPaper('A4', 'portrait');
        return response()->streamDownload(
            fn() => print($pdf->output()),
            "invoice.pdf"
        );
    }
}
