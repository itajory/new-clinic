<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;

class CheckTable extends Component
{
    public $checks;
    public array $headers;
    public $selectAll = false;
    public  $selectedRows = [];




    public function mount(Collection $checks)
    {
        $this->checks = $checks;
        $this->headers = [
            ['key' => 'id', 'label' => 'ID'],
            ['key' => 'bank', 'label' => 'Bank'],
            ['key' => 'account_number', 'label' => 'Account Number'],
            ['key' => 'check_number', 'label' => 'Check #'],
            ['key' => 'amount', 'label' => 'Amount'],
            ['key' => 'date', 'label' => 'Check Date'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'user', 'label' => 'Created By'],
            ['key' => 'replacement', 'label' => 'Replaced By'],
            ['key' => 'actions', 'label' => 'Actions']
        ];
    }
    public function render()
    {
        return view('livewire.components.check-table');
    }

    public function changeShowChecKModal(bool $show, $item)
    {
        $this->dispatch('showCheckModal', $show, $item);
    }
}
