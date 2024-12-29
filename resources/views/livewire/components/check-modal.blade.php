<div>
    <x-mary-modal wire:model="showCheckModal"
        subtitle=""
        box-class="border-2 border-primary"
        persistent
        class="">


        <x-mary-header title="{{ __('change_check_status') }}"
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

            <x-mary-select label="{{ __('check_status') }}"
                :options="$form->statuses"
                icon="c-gift-top"
                placeholder="{{ __('select_check_status') }}"
                placeholder-value="cash"
                option-value="key"
                option-label="label"
                wire:model.live="form.status" />


            @if ($form->status === 'replaced_with_check')
                <div class="flex flex-col gap-2 p-1 bg-gray-100">
                    <p class="font-semibold ">
                        {{ __('replaced_by') }}</p>

                    <x-mary-select label="{{ __('bank') }}"
                        :options="$form->banks"
                        icon="c-gift-top"
                        placeholder="{{ __('select_bank') }}"
                        option-value="id"
                        option-label="name"
                        wire:model.live="form.bank_id" />
                    <x-mary-input label="{{ __('account_number') }}"
                        wire:model="form.account_number" />
                    <x-mary-input label="{{ __('check_number') }}"
                        wire:model="form.check_number" />
                    <x-mary-datetime label="{{ __('check_date') }}"
                        wire:model="form.check_date"
                        type="date" />

                    <x-mary-input label="{{ __('amount') }}"
                        wire:model="form.check_amount"
                        type="currency"
                        readonly />

                </div>
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
