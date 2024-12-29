<div>
    <x-mary-modal wire:model="showAppointmentModal"
        subtitle=""
        box-class="border-2 border-primary"
        persistent
        class="paymentModal">


        @if ($viewAppointment)
            <x-mary-header title="# {{ $viewAppointment['id'] }}"
                subtitle="{{ __('Appointment info') }}"
                size="text-2xl"
                class="mb-1">
                <x-slot:actions>
                    <x-mary-button icon="o-x-mark"
                        wire:click="$parent.showAppointment(null)" />
                </x-slot:actions>
            </x-mary-header>
            <x-mary-hr />
            <div class="grid grid-cols-3 gap-2 mt-16 md:grid-cols-9">
                <div class="flex items-start justify-start col-span-3 gap-2">
                    <p class="font-semibold">{{ __('Created at') }}:</p>
                    <p>{{ $viewAppointment['created_at'] }}</p>
                </div>
                <div class="flex items-start justify-start col-span-3 gap-2">
                    <p class="font-semibold">{{ __('Created by') }}:</p>
                    <p>{{ is_array($viewAppointment['created_by']) ? $viewAppointment['created_by']['name'] : $viewAppointment['created_by'] }}
                    </p>
                </div>
                <div class="flex items-start justify-start col-span-3 gap-2">
                    <p class="font-semibold">{{ __('Updated at') }}:</p>
                    <p>{{ $viewAppointment['updated_at'] }}</p>
                    {{-- <p>{{ \Carbon\Carbon::parse($viewAppointment['updated_at'])->format('Y-m-d') }}
                    </p> --}}

                </div>
                <div class="flex items-start justify-start col-span-3 gap-2">
                    <p class="font-semibold">{{ __('Appointment date') }}:</p>
                    <p>{{ $viewAppointment['appointment_time'] }}</p>
                </div>
                <div class="flex items-start justify-start col-span-3 gap-2">
                    <p class="font-semibold">{{ __('Duration') }}:</p>
                    <p>{{ $viewAppointment['duration'] }}</p>
                </div>
                <div class="flex items-start justify-start col-span-3 gap-2">
                    <p class="font-semibold">{{ __('Status') }}:</p>
                    <p>{{ __($viewAppointment['status']) }}</p>
                </div>
                <div class="flex items-start justify-start col-span-6 gap-2">
                    <p class="font-semibold">{{ __('Medical center') }}:</p>
                    <p>{{ is_array($viewAppointment['medical_center']) ? $viewAppointment['medical_center']['name'] : $viewAppointment['medical_center_id'] }}
                    </p>
                </div>
                <div class="flex items-start justify-start col-span-3 gap-2">
                    <p class="font-semibold">{{ __('Doctor') }}:</p>
                    <p>{{ is_array($viewAppointment['doctor']) ? $viewAppointment['doctor']['name'] : $viewAppointment['doctor'] }}
                    </p>
                </div>
                <div class="flex items-start justify-start col-span-3 gap-2">
                    <p class="font-semibold">{{ __('Price') }}:</p>
                    <p>{{ $viewAppointment['price'] }}</p>
                </div>
                <div class="flex items-start justify-start col-span-6 gap-2">
                    <p class="font-semibold">{{ __('Discount') }}:</p>
                    <p>{{ $viewAppointment['discount'] }}</p>
                </div>

                @if ($viewAppointment['patient_fund'])
                    <div
                        class="flex items-start justify-start col-span-6 gap-2">
                        <p class="font-semibold">{{ __('Patient Fund') }}:</p>
                        <p>{{ is_array($viewAppointment['patient_fund']) ? $viewAppointment['patient_fund']['name'] : $viewAppointment['patient_fund'] }}
                        </p>
                    </div>
                    <div
                        class="flex items-start justify-start col-span-3 gap-2">
                        <p class="font-semibold">
                            {{ __('Patient Fund total') }}:
                        </p>
                        <p>{{ $viewAppointment['patient_fund_total'] }}</p>
                    </div>
                @endif
                <div class="flex items-start justify-start col-span-3 gap-2">
                    <p class="font-semibold">{{ __('Total') }}:</p>
                    <p class="font-semibold">{{ $viewAppointment['total'] }}
                    </p>
                </div>
                <div class="flex items-start justify-start col-span-3 gap-2">
                    <p class="font-semibold">{{ __('Notes') }}:</p>
                    <p>{{ $viewAppointment['note'] }}</p>
                </div>
            </div>
        @endif

    </x-mary-modal>

</div>
