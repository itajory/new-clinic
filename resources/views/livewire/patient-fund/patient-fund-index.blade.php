@php use App\Models\PatientFund; @endphp
<div class="w-full p-6 bg-white rounded-xl">
    <x-mary-header title="{{ __('patient_funds') }}"
        subtitle="{{ __('all_patient_funds') }}"
        separator
        progress-indicator>

        <x-slot:middle
            class="!justify-end ">
            <x-mary-input placeholder="{{ __('search') }}"
                wire:model.blur="searchWord"
                wire:keydown.enter="search"
                class="">
            </x-mary-input>


        </x-slot:middle>
        <x-slot name="actions">
            <x-mary-toggle label="{{ __('show_archived_only') }}"
                wire:model.live="showArchived"
                class="focus:bg-primary/10 focus:border-primary focus:outline-primary focus-within:outline-primary"
                right />
            @can('create', PatientFund::class)
                <x-mary-button icon="o-plus"
                    class="btn-primary btn-circle"
                    @click="$wire.showAddModal">
                </x-mary-button>
            @endcan
        </x-slot>
    </x-mary-header>

    <x-mary-table :headers="$headers"
        :rows="$patientFunds"
        :row-decoration="$this->getRowDecoration()"
        show-empty-text
        empty-text="{{ __('no_data_found') }}">


        @scope('header_id', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope
        @scope('header_name', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('header_contribution_type', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('actions', $contributionType)
            <div class="flex space-x-1">
                @if ($this->showArchived)
                    @can('restore', $contributionType)
                        <x-mary-button icon="c-arrow-up-tray"
                            wire:click="changeShowConfirmModal(true, 'are_you_sure_restore',false,{{ $contributionType['id'] }}) "
                            class=" btn-sm btn-circle btn btn-primary
                                   btn-outline bg-accent hover:!text-white" />
                    @endcan
                @else
                    @can('delete', $contributionType)
                        <x-mary-button icon="s-trash"
                            wire:click="changeShowConfirmModal(true, 'are_you_sure_delete', true, {{ $contributionType['id'] }}) "
                            class=" btn btn-sm btn-circle
                                   btn-error
                                   btn-outline hover:!text-white bg-accent
                       " />
                    @endcan

                    @canany(['update', 'view'], $contributionType)
                        <x-mary-button icon="s-pencil"
                            wire:click="edit({{ $contributionType['id'] }})"
                            class=" btn-sm btn-circle btn-info
                                   btn-outline hover:!text-white bg-accent
                       " />
                    @endcanany
                    @can('view', $contributionType)
                        <x-mary-button icon="c-eye"
                            link="{{ route('patient_fund.view', $contributionType['id']) }}"
                            class=" btn-sm btn-circle
                btn-primary
                btn-outline hover:!text-white bg-accent " />
                    @endcan
                @endif
            @endscope

    </x-mary-table>

    <x-mary-modal wire:model="addModal"
        subtitle=""
        box-class="border-2 border-primary"
        persistent>


        <x-mary-header
            title="{{ $editMode ? __('edit_patient_fund') : __('add_patient_fund') }}"
            subtitle="{{ $editMode ? '' : __('add_patient_fund_subtitle') }}"
            size="text-2xl"
            class="mb-5">
            <x-slot:actions>
                <x-mary-button icon="o-x-mark"
                    @click="$wire.hideModal" />
            </x-slot:actions>
        </x-mary-header>

        <x-mary-form wire:submit="save"
            no-separator>
            <x-mary-input label="{{ __('name') }}"
                wire:model="form.name"
                class="" />

            <p class="font-bold">{{ __('contribution_type') }}</p>
            <ul class="grid w-full grid-cols-2 gap-1">
                @foreach ($contributionTypes as $contributionType)
                    <li>
                        <input type="radio"
                            id="{{ $contributionType }}"
                            name="contribution_type"
                            class="hidden peer"
                            wire:model="form.contribution_type"
                            value="{{ $contributionType }}" />
                        <label for="{{ $contributionType }}"
                            class="inline-flex items-center justify-center w-full p-1 py-3 border cursor-pointer rounded-3xl border-primary peer-checked:border-primary peer-checked:text-primary peer-checked:bg-accent hover:text-primary hover:bg-accent ">
                            <p class="text-sm text-center">
                                {{ $contributionType }}</p>
                        </label>
                    </li>
                @endforeach
            </ul>
            @error('form.contribution_type')
                <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                    {{ $message }}</p>
            @enderror

            <x-slot:actions>
                <x-mary-button label="{{ __('confirm') }}"
                    type="submit"
                    spinner="save"
                    class="w-full mt-3 btn-primary" />
            </x-slot:actions>
        </x-mary-form>

    </x-mary-modal>
    @if ($showConfirmModal)
        <livewire:components.confirm-modal :showModal="$showConfirmModal"
            :message="$confirmMessage"
            :isDelete="$isDelete"
            :key="' ' . now()" />
    @endif
</div>
