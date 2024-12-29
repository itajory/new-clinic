@php use App\Models\Treatment; @endphp
<div class="w-full p-6 bg-white rounded-xl">
    <x-mary-header title="{{ __('treatments') }}"
        subtitle="{{ __('all_treatments') }}"
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
            @can('create', Treatment::class)
                <x-mary-button icon="o-plus"
                    class=" btn-primary btn btn-circle"
                    @click="$wire.showAddModal">
                </x-mary-button>
            @endcan
        </x-slot>
    </x-mary-header>

    <x-mary-table :headers="$headers"
        :rows="$treatments"
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

        @scope('header_price', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('header_duration', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('actions', $treatment)
            <div class="flex space-x-1">

                @if ($this->showArchived)
                    @can('restore', $treatment)
                        <x-mary-button icon="c-arrow-up-tray"
                            wire:click="changeShowConfirmModal(true, 'are_you_sure_restore',false,{{ $treatment['id'] }}) "
                            class=" btn-sm btn-circle btn btn-primary
                                   btn-outline bg-accent hover:!text-white" />
                    @endcan
                @else
                    @can('delete', $treatment)
                        <x-mary-button icon="s-trash"
                            wire:click="changeShowConfirmModal(true, 'are_you_sure_delete', true, {{ $treatment['id'] }}) "
                            class=" btn btn-sm btn-circle
                                   btn-error
                                   btn-outline hover:!text-white bg-accent
                       " />
                    @endcan

                    @canany(['update', 'view'], $treatment)
                        <x-mary-button icon="s-pencil"
                            wire:click="edit({{ $treatment['id'] }})"
                            class=" btn-sm btn-circle btn-info
                                   btn-outline hover:!text-white bg-accent" />
                    @endcanany
                @endif
            </div>
        @endscope

        @scope('cell_duration', $treatment)
            {{ formatDuration($treatment['duration']) }}
        @endscope

    </x-mary-table>

    <x-mary-modal wire:model="addModal"
        subtitle=""
        box-class="border-2 border-primary"
        persistent>


        <x-mary-header
            title="{{ $editMode ? __('edit_treatment') : __('add_treatment') }}"
            subtitle="{{ $editMode ? '' : __('add_treatment_subtitle') }}"
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
            <x-mary-input label="{{ __('price') }}"
                wire:model="form.price"
                type="number"
                class="" />
            {{--            <x-mary-radio label="{{__('duration')}}" --}}
            {{--                          :options="$durations" --}}
            {{--                      wire:model="form.duration" --}}
            {{--                          hint="{{__('by_minutes')}}" --}}
            {{--                            class="border-b-gray-500 focus:border-primary --}}
            {{--                            focus:outline-primary focus-within:outline-primary" --}}
            {{--            /> --}}

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
                            class="inline-flex items-center justify-center w-full p-1 py-2 border cursor-pointer rounded-2xl border-primary peer-checked:border-primary peer-checked:text-primary peer-checked:bg-accent hover:text-primary hover:bg-accent ">
                            <p class="text-sm text-center">
                                {{ formatDuration($duration) }}
                            </p>
                        </label>
                    </li>
                @endforeach
            </ul>
            @error('form.duration')
                <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                    {{ $message }}</p>
            @enderror

            <x-slot:actions>
                <x-mary-button label="{{ __('confirm') }}"
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
