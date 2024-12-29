<x-mary-form wire:submit="save"
    no-separator
    wire:loading.class="opacity-50">
    <div class="grid grid-cols-2 gap-1.5 !text-xs">
        {{-- <x-mary-input label="{{ __('patientName') }}"
            wire:model="form.patient_full_name"
            class=""
            readonly /> --}}
        <div
            class="flex items-center justify-start gap-1 px-1 py-2 rounded bg-cultured">
            <strong>{{ __('patientName') }}</strong>
            <p>{{ $form->patient_full_name }}</p>
        </div>
        <div
            class="flex items-center justify-start gap-1 px-1 py-2 rounded bg-cultured">
            <strong>{{ __('doctorName') }}</strong>
            <p>{{ $form->doctor['name'] }}</p>
        </div>
        {{-- <x-mary-input label="{{ __('doctorName') }}"
            wire:model="form.doctor.name"
            class=""
            readonly /> --}}
        <div
            class="flex items-center justify-start gap-1 px-1 py-2 rounded bg-cultured">
            <strong>{{ __('medicalCenterName') }}</strong>
            <p>{{ $form->medicalCenterName }}</p>
        </div>

        {{-- <x-mary-input label="{{ __('medicalCenterName') }}"
            wire:model="form.medicalCenterName"
            class=""
            readonly /> --}}
        <div
            class="flex items-center justify-start gap-1 px-1 py-2 rounded bg-cultured">
            <strong>{{ __('treatmentName') }}</strong>
            <p>{{ $form->treatmentName }}</p>
        </div>
        {{-- <x-mary-input label="{{ __('treatmentName') }}"
            wire:model="form.treatmentName"
            class=""
            readonly /> --}}
        <div
            class="flex items-center justify-start gap-1 px-1 py-2 rounded bg-cultured">
            <strong>{{ __('appointmentDate') }}</strong>
            <p>{{ $form->appointment_time }}</p>
        </div>
        <div
            class="flex items-center justify-start gap-1 px-1 py-2 rounded bg-cultured">
            <strong>{{ __('to') }}</strong>
            <p>{{ $form->dateTo }}</p>
        </div>
        {{-- <div class="flex flex-wrap justify-between">
            <x-mary-input label="{{ __('appointmentDate') }}"
                wire:model="form.appointment_time"
                class=""
                type="datetime-local"
                readonly />
            <x-mary-input label="{{ __('to') }}"
                wire:model="form.dateTo"
                class=""
                type="datetime-local"
                readonly />
        </div> --}}
        <div class="col-span-2">

            <p class="font-bold">{{ __('duration') }}</p>
            <ul class="grid w-full grid-cols-6 gap-1">
                @foreach ($durations as $duration)
                    <li>
                        <input type="radio"
                            id="{{ $duration }}"
                            name="duration"
                            class="hidden peer"
                            wire:model.live.number="form.duration"
                            value="{{ (int) $duration }}" />
                        <label for="{{ $duration }}"
                            class="inline-flex items-center justify-center w-full p-1 py-2 text-xs border cursor-pointer min-w-12 rounded-2xl border-primary peer-checked:border-primary peer-checked:text-primary peer-checked:bg-accent hover:text-primary hover:bg-accent ">
                            <p class="text-sm text-center">
                                {{ formatDuration($duration) }}
                            </p>
                        </label>
                    </li>
                @endforeach
            </ul>
        </div>

        <x-mary-input label="{{ __('price') }}"
            wire:model.blur.number="form.price"
            class="!text-xs h-12"
            type="number"
            min="0" />
        <x-mary-input label="{{ __('discount (%)') }}"
            wire:model.blur.number="form.discount"
            class="!text-xs h-12"
            type="number"
            min="0"
            max="100" />

        @if ($selectedPatient->patientFunds->count() > 0)
            {{-- <x-mary-select label="{{ __('patient_funds') }}"
            :options="$selectedPatient->patientFunds"
            icon="c-gift-top"
            placeholder="{{ __('select_patient_fund') }}"
            placeholder-value="0"
            wire:model.live.number="form.patient_fund_id" /> --}}

            <x-mary-input label="{{ __('patient_funds') }}"
                value="{{ $selectedPatient->patientFunds[0]->name }}"
                class="!text-xs h-12"
                type="text"
                readonly />
            {{-- <x-mary-input label="{{ __('patient_funds') }}"
            wire:model="form.patient_fund_id"
            value = "selectedPatient->patientFunds[0]->id"
            class=""
            type="number"
            readonly /> --}}

            <x-mary-input label="{{ __('contribution_type') }}"
                wire:model="form.patient_fund_contribution_type"
                class="!text-xs h-12"
                readonly />


            <x-mary-input label="{{ __('patient_fund_amount') }}"
                wire:model.blur.number="form.patient_fund_amount"
                class="!text-xs h-12"
                type="number"
                min="0" />


            <x-mary-input label="{{ __('patient_fund_total') }}"
                wire:model.blur.number="form.patient_fund_total"
                class="!text-xs h-12"
                type="number"
                min="0"
                readonly />
        @else
            <x-mary-input label="{{ __('patient_funds') }}"
                value="{{ __('No Patient Fund') }}"
                class="!text-xs h-12"
                type="text"
                readonly />
        @endif

        <x-mary-input label="{{ __('total') }}"
            wire:model="form.total"
            class="!text-xs h-12"
            type="number"
            readonly />

        <div>
            <x-mary-select label="{{ __('repeat') }}"
                wire:model.live.number="form.repeat"
                :options="$form->repeatArray"
                option-label="id"
                option-value="id"
                class="!text-xs h-12"
                wire:change="setRepeat($event.target.value)" />
            @error('repeat')
                <span class="text-sm text-error">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <x-mary-textarea label="{{ __('note') }}"
        wire:model="form.note"
        placeholder="{{ __('enter_note') }}."
        rows="3" />

    <x-slot:actions>
        <x-mary-button label="Confirm"
            type="submit"
            spinner="save"
            class="w-full mt-3 btn btn-primary" />
    </x-slot:actions>
</x-mary-form>
