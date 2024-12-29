<div class="w-full p-6 text-sm bg-white rounded-lg shadow-md">
    {{--    Header --}}
    <x-mary-header subtitle="">
        <x-slot name="title">
            {{ $patientFund->name }}
        </x-slot>

        <x-slot name="actions">


        </x-slot>
    </x-mary-header>


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
            @if ($activeTab === 'not_closed' && count($selectedAppointments) > 0)
                <x-mary-button icon=""
                    wire:click="closeAppointments"
                    class="btn btn-info ">
                    {{ __('Close') }}
                </x-mary-button>
            @endif
        </x-slot:actions>
    </x-mary-header>

    <div class="h-full overflow-y-auto">
        <livewire:components.patient-financial-table :$appointments
            :$activeTab
            :$headers
            :$parentView
            :key="$activeTab . ' ' . $appointments->count()" />
    </div>

</div>
