<div>
    <x-mary-modal wire:model="showpaymentModal"
        subtitle=""
        box-class="border-2 border-primary"
        persistent
        class="paymentModal ">


        <x-mary-header title="{{ __('add_payment') }}"
            subtitle="{{ '' }}"
            size="text-2xl"
            class="mb-5">
            <x-slot:actions>
                <x-mary-button icon="o-x-mark"
                    wire:click="hideModal" />
            </x-slot:actions>
        </x-mary-header>

        <x-mary-form wire:submit="save"
            no-separator>
            <div class="flex flex-wrap justify-start gap-2 item-center">
                <x-mary-input label="{{ __('amount') }}"
                    wire:model="form.amount"
                    readonly />
                <x-mary-select label="{{ __('payment_type') }}"
                    :options="$form->paymnetTypeOptions"
                    icon="c-gift-top"
                    placeholder="{{ __('select_payment_type') }}"
                    placeholder-value="cash"
                    option-value="key"
                    option-label="label"
                    wire:model.live="form.payment_type" />
                @if ($form->payment_type === 'check')
                    <x-mary-input label="{{ __('checks_count') }}"
                        wire:model.live="form.checksCount"
                        class="" />
                @endif

            </div>
            @if ($form->payment_type === 'cash' || $form->payment_type === 'visa')
                <div>
                    <x-mary-file wire:model="form.attachment"
                        label="{{ __('attachment') }}"
                        accept="application/pdf, image/png, image/jpeg" />
                </div>
            @endif

            @if ($form->payment_type === 'check')
                @if ($form->banks == null || $form->banks->count() == 0)
                    <p>{{ __('you_dont_have_banks_in_your_system') }}</p>
                @else
                    @foreach ($form->checks as $index => $check)
                        <div class="p-1 bg-gray-100">
                            <p class="mb-1 font-semibold">
                                {{ __('check') . ' ' . ($index + 1) }}</p>
                            <div class="grid grid-cols-2 gap-2 md:grid-cols-5">
                                <x-mary-select label="{{ __('bank') }}"
                                    :options="$form->banks"
                                    icon="c-gift-top"
                                    placeholder="{{ __('select_bank') }}"
                                    option-value="id"
                                    option-label="name"
                                    wire:model.live="form.checks.{{ $index }}.bank_id" />
                                <x-mary-input
                                    label="{{ __('account_number') }}"
                                    wire:model="form.checks.{{ $index }}.account_number" />
                                <x-mary-input label="{{ __('check_number') }}"
                                    wire:model="form.checks.{{ $index }}.check_number" />
                                <x-mary-datetime label="{{ __('check_date') }}"
                                    wire:model="form.checks.{{ $index }}.date"
                                    type="date" />

                                <x-mary-input label="{{ __('amount') }}"
                                    wire:model="form.checks.{{ $index }}.amount"
                                    type="currency" />



                            </div>
                        </div>
                    @endforeach
                    @error('checks')
                        <div>
                            <p class="text-red-500">{{ $message }}</p>
                        </div>
                    @enderror
                @endif
            @endif
            <x-slot:actions>

                <x-mary-button label="Confirm"
                    type="submit"
                    spinner="save"
                    class="w-full mt-3 btn btn-primary" />
            </x-slot:actions>
        </x-mary-form>

    </x-mary-modal>

</div>
