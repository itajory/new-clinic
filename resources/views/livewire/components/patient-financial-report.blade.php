<div class="relative w-full h-full pb-14 ">

    <x-mary-header separator
        progress-indicator
        class="text-sm font-medium text-center text-gray-500 ">
        <x-slot:middle
            class=" !justify-start">

            <ul class="flex flex-wrap w-full gap-2 pt-4 overflow-auto">
                @foreach ($tabs as $index => $tab)
                    <li wire:click="setTab('{{ $tab }}')">
                        <p class="inline-block  p-2 {{ $tab == $activeTab ? 'text-white bg-primary' : 'bg-gray-100' }} rounded
                                             hover:text-white hover:bg-primary cursor-pointer min-w-32 gap-6"
                            style="translateX({{ $index * 100 }}%)">
                            {{ $tab }}
                        </p>
                    </li>
                @endforeach
            </ul>
        </x-slot:middle>
        <x-slot:actions
            class="flex h-full px-4 mt-4 items-cenetr">
            <x-mary-button icon="o-funnel"
                wire:click="showFinanceFilterDrawer"
                class="relative">
                @if ($this->filtersCount > 0)
                    <x-mary-badge value="{{ $this->filtersCount }}"
                        class="absolute badge-warning -right-2 -top-2" />
                @endif
            </x-mary-button>
        </x-slot:actions>
    </x-mary-header>
    <div class="h-full overflow-y-auto">

        @if ($activeTab == 'checks')
            <livewire:components.check-table :$checks
                :key="$activeTab . '' . now()" />
        @else
            <livewire:components.patient-financial-table :$appointments
                :$activeTab
                :key="$activeTab .
                    '' .
                    $appointments->count() .
                    ' ' .
                    $dateFrom" />
        @endif
    </div>


    <div
        class="absolute bottom-0 left-0 right-0 z-10 flex items-center gap-3 px-3 py-1 h-14">
        @if ($activeTab == 'not_paid')
            <x-mary-button label="{{ __('pay') . ' ' . ($totalSelected ?? 0) }}"
                class="btn btn-primary w-28"
                wire:click="chanegShowMpaymentModal({{ true }})" />
        @endif

        @if ($appointment_ids && count($appointment_ids) > 0)
            <livewire:components.print-invoice :key="$totalSelected . '' . now()"
                :$appointment_ids
                :$patient />
        @endif
    </div>

    {{-- Modals --}}

    @if ($totalSelected > 0 && $showpaymentModal === true)
        <livewire:components.payment-modal :$showpaymentModal
            :$totalSelected
            :patient_id="$patient->id"
            :$appointment_ids
            :key="$showpaymentModal . '' . $totalSelected" />
    @endif

    @if ($showCheckModal === true && $selectedCheck)
        <livewire:components.check-modal :$showCheckModal
            :$selectedCheck
            :key="$showCheckModal . '' . now()" />
    @endif




    {{-- Filters  --}}

    <livewire:components.financial-filter :$activeTab
        :key="$activeTab . $filtersCount" />
</div>
