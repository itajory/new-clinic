<?php

namespace App\Livewire\Components;

use App\Models\Bank;
use App\Models\Check;
use Livewire\Component;
use App\Livewire\Forms\CheckForm;

class CheckModal extends Component
{
    public CheckForm $form;
    public bool $showCheckModal;
    public $selectedCheck;

    public function mount($showCheckModal, $selectedCheck)
    {
        $this->showCheckModal = $showCheckModal;
        $this->form->banks = Bank::all();
        $this->form->setCheck(Check::find($selectedCheck['id']));
    }
    public function render()
    {
        return view('livewire.components.check-modal');
    }

    public function hideModal()
    {
        $this->dispatch('showCheckModal', false);
    }

    public function save()
    {
        $this->authorize('create', Check::class);
        $this->form->updateCheck();
        $this->dispatch('checkUpdated');
    }
}
