<?php

namespace App\Livewire\Components;

use Livewire\Component;

class ConfirmModal extends Component
{
    public $message = "";
    public bool $isDelete = false;
    public bool $showModal = false;
    public function mount($showModal, $message, $isDelete = false)
    {
        $this->message = $message;
        $this->isDelete = $isDelete;
        $this->showModal = $showModal;
    }

    public function render()
    {
        return view('livewire.components.confirm-modal');
    }

    public function confirm()
    {
        $this->showModal = false;
        if ($this->isDelete) {
            $this->dispatch('confiirmDelete');
        } else {
            $this->dispatch('confirmResetore');
        }
    }

    public function cancel()
    {
        $this->showModal = false;
        $this->dispatch('confeirmReset');
    }
}