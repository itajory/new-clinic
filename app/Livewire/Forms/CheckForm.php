<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Check;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckForm extends Form
{
    public Check $check;

    public string $status = '';

    public $banks;
    public $bank_id;
    public $account_number;
    public $check_number;
    public $check_amount;
    public $check_date;

    public array $statuses = [
        ['key' => 'collected', 'label' => 'Collected'],
        ['key' => 'returned', 'label' => 'Returned'],
        ['key' => 'pending', 'label' => 'Pending'],
        ['key' => 'replaced_with_check', 'label' => 'Replaced With Check'],
        ['key' => 'replaced_with_cash', 'label' => 'Replaced With Cash']
    ];

    protected array $rules = [
        'status' => 'required|in:collected,returned,pending,replaced_with_check,replaced_with_cash'
    ];



    public function setCheck($check)
    {
        $this->check = $check;
        $this->status = $check->status;
        $this->check_amount = $check->amount;
    }

    public function updateCheck()
    {
        $this->validate();

        if ($this->status === 'replaced_with_check') {

            DB::transaction(function () {
                $newCheck = Check::create([
                    'bank_id' => $this->bank_id,
                    'account_number' => $this->account_number,
                    'check_number' => $this->check_number,
                    'amount' => $this->check->amount,
                    'date' => $this->check_date,
                    'status' => 'pending',
                    'created_by' => Auth::id(),
                    'payment_id' => $this->check->payment_id,
                ]);

                $this->check->update([
                    'status' => 'replaced_with_check',
                    'replaced_by' => $newCheck->id
                ]);
            });
        } else {
            $this->check->update([
                'status' => $this->status
            ]);
        }
    }
}
