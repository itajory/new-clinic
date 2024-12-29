<?php

namespace App\View\Components;

use App\Models\Setting;
use Illuminate\View\View;
use Illuminate\View\Component;

class DashLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.dash');
    }
}
