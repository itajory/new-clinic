<?php

namespace App\Livewire\Forms;

use App\Models\Bank;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class BankForm extends Form
{
    public ?Bank $bank = null;
    public string $name = '';
    public string $number = '';

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('banks', 'name')->ignore($this->bank),
            ],
            'number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('banks', 'number')->ignore($this->bank),
            ],
        ];
    }

    public function store(): void
    {
        $this->validate();
        Bank::create(['name' => $this->name, 'number' => $this->number]);
    }

    public function update(): void
    {
        $this->validate();
        $this->bank->update(['name' => $this->name, 'number' => $this->number]);
    }

    public function setBank(Bank $bank): void
    {
        $this->bank = $bank;
        $this->name = $bank->name;
        $this->number = $bank->number;
    }
}
