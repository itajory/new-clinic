@php use App\Models\City; @endphp
<div class="w-full p-6 bg-white rounded-xl">
    <x-mary-header title="{{ __('cities') }}"
        subtitle="{{ __('all_cities') }}"
        separator
        progress-indicator>

        <x-slot:middle
            class="!justify-end ">
            <x-mary-input placeholder="{{ __('search') }}"
                wire:model.blur="searchWord"
                wire:keydown.enter="search">
            </x-mary-input>


        </x-slot:middle>

        <x-slot name="actions">
            <x-mary-toggle label="{{ __('show_archived_only') }}"
                wire:model.live="showArchived"
                class="focus:bg-primary/10 focus:border-primary focus:outline-primary focus-within:outline-primary"
                right />
            @can('create', City::class)
                <x-mary-button icon="o-plus"
                    class="btn-primary btn-circle"
                    @click="$wire.showAddModal">
                </x-mary-button>
            @endcan
        </x-slot>
    </x-mary-header>
    <x-mary-table :headers="$headers"
        :rows="$cities"
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

        @scope('header_actions', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('actions', $city)
            <div class="flex space-x-1">
                @if ($this->showArchived)
                    @can('restore', $city)
                        <x-mary-button icon="c-arrow-up-tray"
                            wire:click="changeShowConfirmModal(true, 'are_you_sure_restore',false,{{ $city['id'] }}) "
                            class=" btn-sm btn-circle btn btn-primary
                                   btn-outline bg-accent hover:!text-white" />
                    @endcan
                @else
                    @can('delete', $city)
                        <x-mary-button icon="s-trash"
                            wire:click="changeShowConfirmModal(true, 'are_you_sure_delete', true, {{ $city['id'] }}) "
                            class=" btn btn-sm btn-circle
                                   btn-error
                                   btn-outline hover:!text-white bg-accent
                       " />
                    @endcan

                    @canany(['update', 'view'], $city)
                        <x-mary-button icon="s-pencil"
                            wire:click="edit({{ $city['id'] }})"
                            class="btn-sm btn-circle btn-info
                                   btn-outline hover:!text-white bg-accent" />
                    @endcanany
                @endif
            </div>
        @endscope

    </x-mary-table>

    <x-mary-modal wire:model="addModal"
        subtitle=""
        box-class="border-2 border-primary"
        persistent>


        <x-mary-header
            title="{{ $editMode ? __('edit_city') : __('add_city') }}"
            subtitle="{{ $editMode ? '' : __('add_city_subtitle') }}"
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


            <x-slot:actions>
                {{--                <x-mary-button label="Cancel" @click="$wire.addModal = false"/> --}}
                <x-mary-button label="Confirm"
                    type="submit"
                    spinner="save"
                    class="w-full mt-3 btn btn-primary" />
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
