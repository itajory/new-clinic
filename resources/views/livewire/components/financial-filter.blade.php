<div>
    <x-mary-drawer wire:model="showFilterDrawer"
        wire:ignore.self
        class="w-11/12 lg:w-1/3 "
        title="{{ __('filter') }}"
        with-close-button
        right
        separator>
        <div class="space-y-2">
            {{-- <x-mary-input placeholder="{{ __('search') }}"
            wire:model.blur="searchWord" /> --}}

            <x-mary-datetime label="{{ __('From Date') }}"
                wire:model.defer="dateFrom"
                icon="o-calendar" />
            <x-mary-datetime label="{{ __('To Date') }}"
                wire:model.defer="dateTo"
                icon="o-calendar" />


            @if ($activeTab == 'checks')
                <x-mary-select label="{{ __('check_status') }}"
                    :options="$checkStatuses"
                    icon="c-briefcase"
                    placeholder="{{ __('select_status') }}"
                    placeholder-value="0"
                    option-value="key"
                    option-label="label"
                    wire:model.defer="checkStatus" />
            @endif

        </div>
        <x-slot:actions>
            @if ($this->filtersCount() > 0)
                <x-mary-button label="{{ __('reset') }}"
                    wire:click="clearFilters"
                    class="btn-warning " />
            @endif
            <x-mary-button label="{{ __('done') }}"
                wire:click='filter'
                class="btn-primary " />
        </x-slot:actions>
    </x-mary-drawer>
</div>
