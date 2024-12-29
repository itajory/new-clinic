<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Bank;
use App\Models\Check;
use App\Models\Payment;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PaymentForm extends Form
{
    use WithFileUploads;

    public $payment_type = 'cash';
    public $amount = 0;
    public $attachment;
    public $created_by;
    public $patient_id;
    public $checksCount = 0;
    public $appointment_ids = [];

    public array $paymnetTypeOptions = [
        ["key" => "cash", "label" => "Cash"],
        ["key" => "visa", "label" => "Visa"],
        ["key" => "check", "label" => "Check"],
        ["key" => "bank_transfer", "label" => "Bank Transfer"]

    ];





    // Check-specific fields
    public $bank_id;
    public $account_number;
    public $check_number;
    public $check_amount;
    public $check_date;
    public $check_status;

    public $banks;
    public $checks = [];

    protected $rules = [
        'payment_type' => 'required|in:cash,visa,check,bank transfer',
        'amount' => 'required|numeric|min:0',
        'attachment' =>  ['nullable', 'file', 'mimes:pdf,docx,doc,jpg,jpeg,png', 'max:5120'],
        'patient_id' => 'required|exists:patients,id',
        'appointment_ids' => 'required|array|min:1',
        'appointment_ids.*' => 'exists:appointments,id',
        'checks.*.bank_id' => 'required_if:payment_type,check|exists:banks,id',
        'checks.*.account_number' => 'required_if:payment_type,check|string',
        'checks.*.check_number' => 'required_if:payment_type,check|string',
        'checks.*.amount' => 'required_if:payment_type,check|numeric|min:0',
        'checks.*.date' => 'required_if:payment_type,check|date|after_or_equal:today',
        'checks.*.status' => 'required_if:payment_type,check|in:collected,returned,pending,replaced_with_check,replaced_with_cash',
    ];

    public function store()
    {
        $this->validate();
        if ($this->payment_type === 'check') {
            $totalCheckAmount = array_sum(array_column($this->checks, 'amount'));

            if ($totalCheckAmount != $this->amount) {
                throw ValidationException::withMessages([
                    'checks' => 'The total amount of all checks must equal the payment amount.',
                ]);
            }
        }
        DB::transaction(function () {

            if ($this->attachment) {
                $this->attachment->store("payment-docs/{$this->patient_id}");
            }


            $payment = Payment::create([
                'payment_type' => $this->payment_type,
                'amount' => $this->amount,
                'attachment' => $this->attachment ? $this->attachment->hashName() : null,
                'created_by' => Auth::id(),
                'patient_id' => $this->patient_id
            ]);

            if ($this->payment_type === 'check') {
                foreach ($this->checks as $check) {
                    Check::create([
                        'bank_id' => $check['bank_id'],
                        'account_number' => $check['account_number'],
                        'check_number' => $check['check_number'],
                        'amount' => $check['amount'],
                        'date' => $check['date'],
                        'status' => $check['status'],
                        'created_by' => Auth::id(),
                        'payment_id' => $payment->id,
                    ]);
                }
            }
            $payment->appointments()->attach($this->appointment_ids);
        });


        session()->flash('message', 'Payment recorded successfully.');


        // Reset form fields
        $this->reset([
            'payment_type',
            'amount',
            'attachment',
            'bank_id',
            'account_number',
            'check_number',
            'check_amount',
            'check_date',
            'check_status'
        ]);
    }

    public function setCheckFields(): void
    {
        $this->checks = [];
        if ($this->payment_type === 'check') {
            foreach (range(1, $this->checksCount) as $index) {
                $this->checks[] = [
                    'bank_id' => '',
                    'account_number' => '',
                    'check_number' => '',
                    'amount' => '',
                    'date' => '',
                    'status' => 'pending',
                ];
            };
        }
    }
}
