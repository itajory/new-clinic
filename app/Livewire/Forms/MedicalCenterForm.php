<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\MedicalCenter;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Illuminate\Database\Eloquent\Collection;

class MedicalCenterForm extends Form
{
    public ?MedicalCenter $medicalCenter = null;
    public string $name = '';
    public string $phone = '';
    public string $fax = '';
    public string $whatsapp = '';
    public string $email = '';
    public int $city_id = 0;
    public array $selectedWorkingHours = [
        [
            'day_of_week' => 1,
            'opening_time' => '00:00',
            'closing_time' => '00:00',
            'selected' => false
        ],
        [
            'day_of_week' => 2,
            'opening_time' => '00:00',
            'closing_time' => '00:00',
            'selected' => false

        ],
        [
            'day_of_week' => 3,
            'opening_time' => '00:00',
            'closing_time' => '00:00',
            'selected' => false

        ],
        [
            'day_of_week' => 4,
            'opening_time' => '00:00',
            'closing_time' => '00:00',
            'selected' => false

        ],
        [
            'day_of_week' => 5,
            'opening_time' => '00:00',
            'closing_time' => '00:00',
            'selected' => false

        ],
        [
            'day_of_week' => 6,
            'opening_time' => '00:00',
            'closing_time' => '00:00',
            'selected' => false

        ],
        [
            'day_of_week' => 7,
            'opening_time' => '00:00',
            'closing_time' => '00:00',
            'selected' => false

        ],
    ];

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('medical_centers', 'name')->ignore($this->medicalCenter?->id),],
            'phone' => ['required', 'string', 'max:255'],
            'fax' => ['required', 'string', 'max:255'],
            'whatsapp' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
        ];
    }

    public function store(): void
    {
        $this->validate();
        $medicalCenter = MedicalCenter::create([
            'name' => $this->name,
            'phone' => $this->phone,
            'fax' => $this->fax,
            'whatsapp' => $this->whatsapp,
            'email' => $this->email,
            'city_id' => $this->city_id,
        ]);
        $filteredWorkingHours = array_filter($this->selectedWorkingHours, fn($workingHour) => $workingHour['selected']);
        $medicalCenter->workingHours()->createMany($filteredWorkingHours);
    }

    public function update(): void
    {
        $this->validate();
        $this->medicalCenter->update([
            'name' => $this->name,
            'phone' => $this->phone,
            'fax' => $this->fax,
            'whatsapp' => $this->whatsapp,
            'email' => $this->email,
            'city_id' => $this->city_id,
        ]);
        $this->medicalCenter->workingHours()->delete();
        $filteredWorkingHours = array_filter($this->selectedWorkingHours, fn($workingHour) => $workingHour['selected']);
        $this->medicalCenter->workingHours()->createMany($filteredWorkingHours);
    }

    public function setMedicalCenter(MedicalCenter $medicalCenter): void
    {
        $this->medicalCenter = $medicalCenter;
        $this->name = $medicalCenter->name;
        $this->phone = $medicalCenter->phone;
        $this->fax = $medicalCenter->fax;
        $this->whatsapp = $medicalCenter->whatsapp;
        $this->email = $medicalCenter->email;
        $this->city_id = $medicalCenter->city_id;
        //        $this->selectedWorkingHours = $medicalCenter->workingHours->toArray();
        if ($medicalCenter->workingHours) {
            $this->mergeWorkingHours($medicalCenter->workingHours->toArray());
        }
    }

    private function mergeWorkingHours(array $selectedWorkingHours2): void
    {
        if (empty($selectedWorkingHours2)) {
            return;
        }
        $this->selectedWorkingHours = array_map(function ($workingHour) use ($selectedWorkingHours2) {
            foreach ($selectedWorkingHours2 as $workingHour2) {
                if ($workingHour['day_of_week'] === $workingHour2['day_of_week']) {
                    $workingHour['opening_time'] = $workingHour2['opening_time'];
                    $workingHour['closing_time'] = $workingHour2['closing_time'];
                    $workingHour['selected'] = true;
                }
            }
            return $workingHour;
        }, $this->selectedWorkingHours);
    }
}
