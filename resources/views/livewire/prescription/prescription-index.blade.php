@php use App\Models\PrescriptionTemplate; @endphp
<div class="w-full p-6 bg-white rounded-xl">
    <x-mary-header title="{{ __('prescription_templates') }}"
        subtitle="{{ __('all_prescriptions') }}"
        separator
        progress-indicator>

        <x-slot:middle
            class="!justify-end ">
            <x-mary-input placeholder="{{ __('search') }}"
                wire:model.blur="searchWord"
                wire:keydown.enter="prescriptions">
            </x-mary-input>
        </x-slot:middle>

        <x-slot name="actions">
            <x-mary-button icon="o-funnel"
                class="relative btn-circle"
                @click="$wire.showFilterDrawer = true">
                @if ($this->filtersCount() > 0)
                    <x-mary-badge value="{{ $this->filtersCount() }}"
                        class="absolute badge-warning -right-2 -top-2" />
                @endif
            </x-mary-button>
            @can('create', PrescriptionTemplate::class)
                <x-mary-button icon="o-plus"
                    class="btn-primary btn-circle"
                    @click="$wire.showAddModal">
                </x-mary-button>
            @endcan
        </x-slot>
    </x-mary-header>
    {{--    drawer filter --}}
    <x-mary-drawer wire:model="showFilterDrawer"
        wire:ignore.self
        class="w-11/12 lg:w-1/3"
        title="{{ __('filter') }}"
        with-close-button
        right
        separator>
        <div class="space-y-2">
            <x-mary-input placeholder="{{ __('search') }}"
                wire:model.blur="searchWord" />
            <x-mary-select label="{{ __('treatment') }}"
                :options="$treatments"
                icon="c-briefcase"
                placeholder="{{ __('select_treatment') }}"
                placeholder-value="0"
                wire:model.live="filterByTreatment"
                class="border-b-gray-500 focus:border-primary focus:outline-primary focus-within:outline-primary" />
            <x-mary-toggle label="{{ __('show_archived_only') }}"
                wire:model.live="showArchived"
                class="focus:bg-primary/10 focus:border-primary focus:outline-primary focus-within:outline-primary "
                right
                tight />
        </div>
        <x-slot:actions>
            @if ($this->filtersCount() > 0)
                <x-mary-button label="{{ __('reset') }}"
                    wire:click="clearFilters"
                    class="btn-warning" />
            @endif
            <x-mary-button label="{{ __('done') }}"
                @click="$wire.showFilterDrawer = false"
                class="btn-primary " />

            {{--            <x-mary-button label="{{__('close')}}" --}}
            {{--                           @click="$wire.showFilterDrawer = false"/> --}}
        </x-slot:actions>
    </x-mary-drawer>


    <x-mary-table :headers="$headers"
        :rows="$this->prescriptions()"
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

        @scope('header_content', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('header_treatment.name', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope


        @scope('actions', $prescription)
            <div class="flex space-x-1">

                @if ($this->showArchived)
                    @can('restore', $prescription)
                        <x-mary-button icon="c-arrow-up-tray"
                            wire:click="changeShowConfirmModal(true, 'are_you_sure_restore',false,{{ $prescription['id'] }}) "
                            class=" btn-sm btn-circle btn btn-primary
                                   btn-outline bg-accent hover:!text-white" />
                    @endcan
                @else
                    @can('delete', $prescription)
                        <x-mary-button icon="s-trash"
                            wire:click="changeShowConfirmModal(true, 'are_you_sure_delete', true, {{ $prescription['id'] }}) "
                            class=" btn btn-sm btn-circle
                                   btn-error
                                   btn-outline hover:!text-white bg-accent
                       " />
                    @endcan
                    @canany(['update', 'view'], $prescription)
                        <x-mary-button icon="s-pencil"
                            wire:click="edit
        ({{ $prescription['id'] }})
"
                            class=" btn-sm btn-circle btn-info
                                   btn-outline hover:!text-white bg-accent
                       " />
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
            title="{{ $editMode ? __('edit_prescription') : __('add_prescription') }}"
            subtitle="{{ $editMode ? '' : __('add_prescription_subtitle') }}"
            size="text-2xl"
            class="mb-5">
            <x-slot:actions>
                <x-mary-button icon="o-x-mark"
                    @click="$wire.hideModal" />
            </x-slot:actions>
        </x-mary-header>

        <x-mary-form wire:submit="save"
            no-separator>
            <x-mary-select label="{{ __('treatment') }}"
                :options="$treatments"
                {{-- icon="c-briefcase" --}}
                placeholder="{{ __('select_treatment') }}"
                placeholder-value="0"
                wire:model="form.treatment_id"
                class="border-b-gray-500 focus:border-primary focus:outline-primary focus-within:outline-primary" />
            <x-mary-input label="{{ __('name') }}"
                wire:model="form.name"
                class="" />
            <x-mary-textarea hint="{{ __('content_hint') }}"
                rows="4"
                label="{{ __('content') }}"
                wire:model="form.content"
                class="" />


            @if ($editMode)
                @can('update', $form->prescriptionTemplate)
                    <x-slot:actions>
                        <x-mary-button label="{{ __('confirm') }}"
                            type="submit"
                            spinner="save"
                            class="w-full mt-3 btn btn-primary" />
                    </x-slot:actions>
                @endcan
            @else
                @can('create', PrescriptionTemplate::class)
                    <x-slot:actions>
                        <x-mary-button label="{{ __('confirm') }}"
                            type="submit"
                            spinner="save"
                            class="w-full mt-3 btn btn-primary" />
                    </x-slot:actions>
                @endcan
            @endif
        </x-mary-form>

    </x-mary-modal>
    @if ($showConfirmModal)
        <livewire:components.confirm-modal :showModal="$showConfirmModal"
            :message="$confirmMessage"
            :isDelete="$isDelete"
            :key="' ' . now()" />
    @endif
</div>
