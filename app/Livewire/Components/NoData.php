<?php

namespace App\Livewire\Components;

use Livewire\Component;

class NoData extends Component
{
    public function render()
    {
        return <<<'HTML'
        <div class="flex justify-center items-center h-96">
            <p class="text-gray-500">{{__('no_data_found')}}</p>
        </div>
        HTML;
    }
}
