<?php

namespace App\Livewire\Patient;

use App\Livewire\Forms\PatientForm;
use App\Models\City;
use App\Models\Patient;
use App\Models\PatientFund;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Mary\Traits\Toast;

class CreatePatient extends Component
{
    use Toast;

    public PatientForm $form;
    public string $successMessage = '';
    public string $searchPatientFundWord = '';
    public Collection $cities;
    private Collection $patientFunds;
    public int $editPatientId = 0;
    public bool $isNested = false;
    public string $searchCityWord = '';

    public function mount(int $id = 0, ?bool $isNested = false)
    {
        $this->searchCityWord = '';
        if ($id > 0) {
            $this->editPatientId = $id;
            $patient = Patient::find($id);
            $this->authorize('update', $patient);
            $this->form->setPatient($patient);
        } else {
            $this->authorize('create', Patient::class);
        }
        $this->cities = City::all();
        $this->isNested = $isNested;
    }

    public function updatedSearchCityWord()
    {
        $this->cities = City::where('name', 'like', '%' . $this->searchCityWord . '%')->get();
    }

    public function save(): void
    {
        $patient = null;
        if ($this->editPatientId > 0) {
            $this->authorize('update', $this->form->patient);
            $patient = $this->form->update();
            $this->successMessage = trans('Patient updated successfully');
        } else {
            $this->authorize('create', Patient::class);
            $patient = $this->form->store();
            $this->successMessage = trans('Patient created successfully');
        }
        $this->success(
            "<u>{$this->successMessage}</u>",
            position: 'bottom-end',
            icon: 'c-check',
            css: 'bg-primary text-white'
        );
        if ($this->isNested) {
            $this->dispatch('patient-saved', $patient);
        } else {
            $this->redirect(route('patient.index'), navigate: true);
        }
    }

    public function patientFunds()
    {
        return PatientFund::where(function ($query) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->searchPatientFundWord) . '%'])
                ->orWhere('id', 'like', '%' . $this->searchPatientFundWord . '%');
        })
            ->get();
    }

    public function cancel()
    {
        if ($this->isNested) {
            $this->dispatch('patient-canceled');
        } else {
            $this->reset();
            $this->redirect(route('patient.index'), navigate: true);
        }
    }

    public function addPatientFund($fund)
    {
        $fund['contribution_percentage'] = 0;
        foreach ($this->form->patientFunds as $existingFund) {
            if ($existingFund['id'] === $fund['id']) {
                return;
            }
        }
        $this->form->patientFunds[] = $fund;
    }

    public function removePatientFund(int $index): void
    {
        unset($this->form->patientFunds[$index]);
        $this->form->patientFunds = array_values($this->form->patientFunds); // Reindex the array

        //        unset($this->form->patientFunds[$index]);
    }

    public function render()
    {
        return view('livewire.patient.create-patient')->layout('layouts.dash');
    }

    public function selectCity($city)
    {
        $this->form->city_id = $city['id'];
        $this->form->city_name = $city['name'];
    }
}
