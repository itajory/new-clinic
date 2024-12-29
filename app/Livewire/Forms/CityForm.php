<?php

namespace App\Livewire\Forms;

use App\Models\City;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CityForm extends Form
{
    public ?City $city = null;
    public string $name = '';

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cities', 'name')->ignore($this->city?->id),
            ],
        ];
    }

    public function store(): void
    {
        $this->validate();
        City::create(['name' => $this->name]);
    }

    public function update(): void
    {
        $this->validate();
        $this->city->update(['name' => $this->name]);
    }

    public function setCity(City $city): void
    {
        $this->city = $city;
        $this->name = $city->name;
    }
}
