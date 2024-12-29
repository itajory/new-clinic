<div class=" grid grid-cols-2 gap-1.5 !text-xs"
     wire:loading.class="opacity-50">
    <div
            class="flex items-center justify-start gap-1 px-1 py-2 rounded bg-cultured ">
        <div class="font-semibold">{{ __('appointment no') }}</div>
        <div class="ms-1">{{ $selectedAppointment?->id }}</div>
    </div>
    <div
            class="flex items-center justify-start gap-1 px-1 py-2 rounded bg-cultured ">
        <div class="font-semibold">{{ __('patientName') }}</div>
        <div class="ms-1">{{ $selectedPatient?->full_name }}</div>
    </div>
    <div
            class="flex items-center justify-start gap-1 px-1 py-2 rounded bg-cultured ">
        <div class="font-semibold">{{ __('doctorName') }}</div>
        <div class="ms-1">{{ $selectedDoctor?->name }}</div>
    </div>
    <div
            class="flex items-center justify-start gap-1 px-1 py-2 rounded bg-cultured ">
        <div class="font-semibold">{{ __('medicalCenterName') }}</div>
        <div class="ms-1">{{ $selectedMedicalCenter['name'] }}</div>
    </div>
    <div
            class="flex items-center justify-start gap-1 px-1 py-2 rounded bg-cultured ">
        <div class="font-semibold">{{ __('appointmentDate') }}</div>
        <div class="ms-1">{{ $selectedAppointment?->appointment_time }}</div>
    </div>
    <div
            class="flex items-center justify-start gap-1 px-1 py-2 rounded bg-cultured ">
        <div class="font-semibold">{{ __('duration') }}</div>
        <div class="ms-1">{{ $selectedAppointment?->duration }}</div>
    </div>
    <div
            class="flex items-center justify-start gap-1 px-1 py-2 rounded bg-cultured ">
        <div class="font-semibold">{{ __('price') }}</div>
        <div class="ms-1">{{ $selectedAppointment?->price }}</div>
    </div>
    <div
            class="flex items-center justify-start gap-1 px-1 py-2 rounded bg-cultured ">
        <div class="font-semibold">{{ __('discount (%)') }}</div>
        <div class="ms-1">{{ $selectedAppointment?->discount }}</div>
    </div>
    @if ($patientFund?->name)
        <div
                class="flex items-center justify-start gap-1 px-1 py-2 rounded bg-cultured ">
            <div class="font-semibold">{{ __('patient_fund') }}</div>
            <div class="ms-1">{{ $patientFund?->name }}</div>
        </div>
        <div
                class="flex items-center justify-start gap-1 px-1 py-2 rounded bg-cultured ">
            <div class="font-semibold">{{ __('patient_fund_total') }}</div>
            <div class="ms-1">{{ $selectedAppointment?->patient_fund_total }}
            </div>
        </div>
    @endif
    <div
            class="flex items-center justify-start gap-1 px-1 py-2 rounded bg-cultured ">
        <div class="font-semibold">{{ __('total') }}</div>
        <div class="ms-1">{{ $selectedAppointment?->total }}</div>
    </div>
    <div
            class="flex items-center justify-start gap-1 px-1 py-2 rounded bg-cultured ">
        <div class="font-semibold">{{ __('repeat') }}</div>
        <div class="ms-1">
            @if ($selectedAppointment?->repeat)
                {{ $selectedAppointment?->repeat }}
                {{-- <svg xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="text-success">
                    <path d="M21.801 10A10 10 0 1 1 17 3.335" />
                    <path d="m9 11 3 3L22 4" />
                </svg> --}}
            @else
                {{ __('rpeated by oppointment no. ' . $selectedAppointment?->repeat_id) }}
                {{-- <svg xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="text-error">
                    <circle cx="12"
                        cy="12"
                        r="10" />
                    <path d="m15 9-6 6" />
                    <path d="m9 9 6 6" />
                </svg> --}}
            @endif

        </div>
    </div>
    <div
            class="flex items-center justify-start col-span-2 gap-1 px-1 py-2 rounded bg-cultured">
        <div class="font-semibold">{{ __('notes') }}</div>
        <div class="ms-1">{{ $selectedAppointment?->note }}</div>
    </div>
    {{-- <div class="space-y-1">
        <div class="font-semibold">{{ __('appointment_status') }}</div>
        <div class="ms-1">{{ $selectedAppointment->status }}</div>
    </div> --}}

    <x-mary-form wire:submit="updateStatus"
                 no-separator
                 class="col-span-2 ">
        <div>
            <x-mary-select label="{{ __('status') }}"
                           wire:model.live="form.status"
                           :options="$form->allStatus"
                           option-label="id"
                           option-value="id"
                           :disabled="in_array($form->appointment->status, [
                    'completed',
                    'not_attended_with_telling',
                    'not_attended_without_telling',
                ])
                    ? true
                    : false"/>
            @error('status')
            <span class="text-sm text-error">{{ $message }}</span>
            @enderror
        </div>
        @if ($form->showIsCost)
            <x-mary-toggle label="{{ __('there_is_cost') }}"
                           wire:model.live="form.isCost"
                           class="focus:bg-primary/10 focus:border-primary focus:outline-primary focus-within:outline-primary"
                           right
                           wire:key="is-cost-toggle"/>
        @endif
        @if ($form->isCost)
            <div class="flex w-full gap-2">

                <x-mary-input label="{{ __('price') }}"
                              wire:model.blur.number="form.price"
                              class="w-1/2"
                              type="number"
                              min="0"/>
                <div class="disc-nput ">

                    <x-mary-input label="{{ __('discount (%)') }}"
                                  wire:model.blur.number="form.discount"
                                  class=""
                                  type="number"
                                  min="0"
                                  max="100"/>
                </div>
            </div>
            <div class="flex w-full gap-2">

                <x-mary-input label="{{ __('contribution_type') }}"
                              wire:model="form.patient_fund_contribution_type"
                              class="w-1/2"
                              readonly/>


                <x-mary-input label="{{ __('patient_fund_amount') }}"
                              wire:model.blur.number="form.patient_fund_amount"
                              class="w-1/2"
                              type="number"
                              min="0"/>
            </div>

            <div class="flex w-full gap-2">

                <x-mary-input label="{{ __('patient_fund_total') }}"
                              wire:model.blur.number="form.patient_fund_total"
                              class="w-1/2"
                              type="number"
                              min="0"
                              readonly/>

                <x-mary-input label="{{ __('total') }}"
                              wire:model="form.total"
                              class="w-1/2"
                              type="number"
                              readonly/>
            </div>
        @endif

        <x-mary-textarea label="{{ __('note') }}"
                         wire:model="form.note"
                         placeholder="{{ __('enter_note') }}."
                         rows="2"/>


        @if (
            !in_array($form->appointment->status, [
                'completed',
                'not_attended_with_telling',
                'not_attended_without_telling',
            ]))
            <x-slot:actions>
                <x-mary-button label="Confirm"
                               type="submit"
                               spinner="save"
                               class="w-full mt-3 btn btn-primary"/>
            </x-slot:actions>
        @endif
    </x-mary-form>
</div>
