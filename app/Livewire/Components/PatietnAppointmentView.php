<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Illuminate\Support\Str;

class PatietnAppointmentView extends Component
{

    public $viewAppointment;
    public $showAppointmentModal = false;

    public function mount($viewAppointment, $showAppointmentModal)
    {
        $this->viewAppointment = $viewAppointment;
        if ($viewAppointment) {

            $this->viewAppointment['updated_at'] = $viewAppointment['updated_at'] ? Str::substr($viewAppointment['updated_at'], 0, 10) : '';
            $this->viewAppointment['created_at'] = Str::substr($viewAppointment['created_at'], 0, 10);
        }

        $this->showAppointmentModal = $showAppointmentModal;
    }

    public function render()
    {
        return view('livewire.components.patietn-appointment-view');
    }
}
