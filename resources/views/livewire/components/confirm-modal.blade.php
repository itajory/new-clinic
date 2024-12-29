<x-mary-modal wire:model="showModal"
    box-class="border-2 border-primary "
    persistent>
    <div>{{ __($message) }}</div>
    <x-slot:actions>
        <x-mary-button label="{{ __('Cancel') }}"
            @click="$wire.cancel" />
        <x-mary-button label="{{ __('Confirm') }}"
            class="{{ $isDelete ? 'btn-error ' : ' btn-info ' }}"
            @click="$wire.confirm" />
    </x-slot:actions>
</x-mary-modal>
