<?php

namespace App\Livewire\Forms;

use App\Models\PatientFund;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class PaitientFundForm extends Form
{
    public ?PatientFund $patientFund = null;
    public string $name = '';
    public string $contribution_type = '';

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('patient_funds', 'name')->ignore($this->patientFund?->id),
            ],
            'contribution_type' => ['required', 'string', 'max:255', Rule::in(['percentage', 'fixed'])
            ],
        ];
    }

    public function store(): void
    {
        $this->validate();
        PatientFund::create(['name' => $this->name, 'contribution_type' => $this->contribution_type]);
    }

    public function update(): void
    {
        $this->validate();
        $this->patientFund->update(['name' => $this->name, 'contribution_type' => $this->contribution_type]);
    }

    public function setPatientFund(PatientFund $patientFund): void
    {
        $this->patientFund = $patientFund;
        $this->name = $patientFund->name;
        $this->contribution_type = $patientFund->contribution_type;
    }


}
