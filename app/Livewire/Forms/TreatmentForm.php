<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Treatment;
use Livewire\Attributes\Validate;

class TreatmentForm extends Form
{
    public ?Treatment $treatment = null;
    public string $name = '';
    public  $price = 0;
    public int $duration = 0;

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'duration' => [
                'required',
                'numeric',
                'min:15',
                'in:15,30,45,60,75,90,105,120,135,150,165,180',

            ],
        ];
    }

    public function updatedDuration($value)
    {
        $this->duration = (int) $value;
    }

    public function store(): void
    {
        $this->validate();
        Treatment::create([
            'name' => $this->name,
            'price' => $this->price,
            'duration' => $this->duration,
        ]);
    }

    public function update(): void
    {
        $this->validate();
        $this->treatment->update([
            'name' => $this->name,
            'price' => $this->price,
            'duration' => $this->duration,
        ]);
    }

    public function setTreatment(Treatment $treatment): void
    {
        $this->treatment = $treatment;
        $this->name = $treatment->name;
        $this->price = $treatment->price;
        $this->duration = $treatment->duration;
    }
}
