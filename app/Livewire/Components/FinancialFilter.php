<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\Attributes\On;

class FinancialFilter extends Component
{
    public $checkStatuses = [];
    public $checkStatus = '';
    public string|null $dateFrom = null;
    public string|null $dateTo = null;
    public bool $showFilterDrawer = false;
    public string $activeTab = '';


    public function mount($activeTab)
    {
        $this->checkStatuses = [
            ['key' => 'collected', 'label' => trans('Collected')],
            ['key' => 'returned', 'label' => trans('Returned')],
            ['key' => 'pending', 'label' => trans('Pending')],
            ['key' => 'replaced_with_check', 'label' => trans('Replaced With Check')],
            ['key' => 'replaced_with_cash', 'label' => trans('Replaced With Cash')]
        ];
    }
    public function render()
    {
        return view('livewire.components.financial-filter');
    }

    public function filtersCount(): int
    {
        $filters = [
            $this->checkStatus !== '',
            $this->dateFrom !== null,
            $this->dateTo !== null,

        ];
        return count(array_filter($filters));
    }


    public function filter()
    {
        $this->dispatch('filterFinances', $this->dateFrom, $this->dateTo, $this->checkStatus, $this->filtersCount());
        $this->showFilterDrawer = false;
    }

    public function clearFilters()
    {
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->checkStatus = '';
        $this->showFilterDrawer = false;

        $this->dispatch('filterFinances');
    }

    #[On('showFinanceFilterDrawer')]
    public function showFinanceFilterDrawer($dateFrom = null, $dateTo = null, $checkStatus = '')
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->checkStatus = $checkStatus;
        $this->showFilterDrawer = true;
    }
}
