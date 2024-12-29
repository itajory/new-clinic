<?php

namespace App\Livewire\Forms;

use App\Models\PrescriptionTemplate;
use Livewire\Attributes\Validate;
use Livewire\Form;

class PrescriptionForm extends Form
{
    public ?PrescriptionTemplate $prescriptionTemplate = null;
    public string $name = '';
    public string $content = '';
    public int $treatment_id = 0;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string' , 'max:255'],
            'treatment_id' => [ 'nullable','integer'],
        ];
    }

    public function store(): void
    {
        $this->validate();
        $data = [
            'name' => $this->name,
            'content' => $this->content,
        ];
        if ($this->treatment_id) {
            $data['treatment_id'] = $this->treatment_id;
        }
        PrescriptionTemplate::create($data);

    }

    public function update(): void
    {
        $this->validate();
        $data = [
            'name' => $this->name,
            'content' => $this->content,
        ];
        if ($this->treatment_id) {
            $data['treatment_id'] = $this->treatment_id;
        } else {
            $data['treatment_id'] = null;
        }
        $this->prescriptionTemplate->update($data);
    }

    public function setPrescriptionTemplate(PrescriptionTemplate $prescriptionTemplate): void
    {
        $this->prescriptionTemplate = $prescriptionTemplate;
        $this->name = $prescriptionTemplate->name;
        $this->content = $prescriptionTemplate->content;
        if($prescriptionTemplate->treatment_id){

        $this->treatment_id = $prescriptionTemplate->treatment_id;
        }
    }

}
