<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Patient;
use App\enums\GenderEnum;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Date;

class PatientForm extends Form
{
    public ?Patient $patient = null;
    public string $full_name = '';
    public GenderEnum $gender = GenderEnum::MALE;
    public string $birth_date = '';
    public string $id_number = '';
    public string $guardian_phone = '';
    public string $patient_phone = '';
    public int $city_id = 0;
    public string $city_name = '';

    public array $patientFunds = [];

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'birth_date' => 'required|date|before:' . Date::now(),
            'id_number' => 'required|string|max:255',
            'guardian_phone' => 'required|string|max:255',
            'patient_phone' => 'nullable|string|max:255',
            'city_id' => 'required|integer|exists:cities,id',
            'patientFunds.*.contribution_percentage' => 'required|numeric|min:0',
        ];
    }

    public function store()
    {
        $this->validate();
        $patient = Patient::create([
            'full_name' => $this->full_name,
            'gender' => $this->gender->value,
            'birth_date' => $this->birth_date,
            'id_number' => $this->id_number,
            'guardian_phone' => $this->guardian_phone,
            'patient_phone' => $this->patient_phone,
            'city_id' => $this->city_id,
        ]);
        foreach ($this->patientFunds as $fund) {
            $patient->patientFunds()->attach(
                $fund['id'],
                ['contribution_percentage' => $fund['contribution_percentage']]
            );
        }
        return $patient;
    }

    public function update()
    {
        $this->validate();
        $this->patient->update([
            'full_name' => $this->full_name,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'id_number' => $this->id_number,
            'guardian_phone' => $this->guardian_phone,
            'patient_phone' => $this->patient_phone,
            'city_id' => $this->city_id,
        ]);
        $syncData = [];
        foreach ($this->patientFunds as $fund) {
            $syncData[$fund['id']] = ['contribution_percentage' => $fund['contribution_percentage']];
        }

        $this->patient->patientFunds()->sync($syncData);
        return $this->patient;
    }

    public function setPatient(Patient $patient): void
    {
        $this->patient = $patient;
        $this->full_name = $patient->full_name;
        $this->gender = $patient->gender;
        $this->birth_date = $patient->birth_date;
        $this->id_number = $patient->id_number;
        $this->guardian_phone = $patient->guardian_phone;
        $this->patient_phone = $patient->patient_phone;
        $this->city_id = $patient->city_id;
        $this->city_name = $patient->city->name;
        $this->patientFunds = array_map(function ($patientFund) {
            return [
                ...$patientFund,
                'contribution_percentage' => $patientFund['pivot']['contribution_percentage'],
            ];
        }, $patient->patientFunds->toArray());
        //        $this->patientFunds = $patient->patientFunds->mapWithKeys(function ($patientFund) {
        //            return [$patientFund->id => true];
        //        })->toArray();
    }

    public function hasData(): bool
    {
        return !empty($this->full_name) ||
            !empty($this->gender) ||
            !empty($this->birth_date) ||
            !empty($this->id_number) ||
            !empty($this->guardian_phone) ||
            !empty($this->patient_phone) ||
            $this->city_id !== 0 ||
            !empty($this->patientFunds);
    }
}
