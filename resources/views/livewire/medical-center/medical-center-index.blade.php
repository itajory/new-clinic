@php use App\Models\MedicalCenter; @endphp
<div class="w-full p-6 bg-white rounded-xl">
    <x-mary-header title="{{ __('medical_centers') }}"
        subtitle="{{ __('all_medical_centers') }}"
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
            @can('create', MedicalCenter::class)
                <x-mary-button icon="o-plus"
                    class="rounded-full bg-primary"
                    @click="$wire.showAddModal">
                </x-mary-button>
            @endcan
        </x-slot>
    </x-mary-header>
    <x-mary-table :headers="$headers"
        :rows="$medicalCenters"
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

        @scope('header_phone', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope
        @scope('header_city.name', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope
        @scope('header_email', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope
        @scope('header_whatsapp', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope
        @scope('header_fax', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('actions', $medicalCenter)
            <div class="flex space-x-1">
                @if ($this->showArchived)
                    @can('restore', $medicalCenter)
                        <x-mary-button icon="c-arrow-up-tray"
                            wire:click="changeShowConfirmModal(true, 'are_you_sure_restore',false,{{ $medicalCenter['id'] }}) "
                            class=" btn-sm btn-circle btn btn-primary
                                   btn-outline bg-accent hover:!text-white" />
                    @endcan
                @else
                    @can('delete', $medicalCenter)
                        <x-mary-button icon="s-trash"
                            wire:click="changeShowConfirmModal(true, 'are_you_sure_delete', true, {{ $medicalCenter['id'] }}) "
                            class=" btn btn-sm btn-circle
                                   btn-error
                                   btn-outline hover:!text-white bg-accent
                       " />
                    @endcan
                    @canany(['update', 'view'], $medicalCenter)
                        <x-mary-button icon="s-pencil"
                            wire:click="edit
        ({{ $medicalCenter['id'] }})
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
        box-class="bg-white border-2 border-primary"
        persistent>


        <x-mary-header
            title="{{ $editMode ? __('edit_medical_center') : __('add_medical_center') }}"
            subtitle="{{ $editMode ? '' : __('add_medical_center_subtitle') }}"
            size="text-2xl"
            class="mb-5">
            <x-slot:actions>
                <x-mary-button icon="o-x-mark"
                    @click="$wire.hideModal" />
            </x-slot:actions>
        </x-mary-header>

        <x-mary-form wire:submit="save"
            no-separator>
            <div class="grid grid-cols-2 gap-3 space-x-1">
                <x-mary-input wire:model="form.name"
                    class="bg-inherit"
                    type="text">
                    <x-slot:label>
                        <span class="font-semibold text-gray-500">
                            {{ __('name') }}</span>
                    </x-slot:label>
                </x-mary-input>
                <x-mary-input wire:model="form.phone"
                    class="bg-inherit"
                    type="tel">
                    <x-slot:label>
                        <span class="font-semibold text-gray-500">
                            {{ __('phone') }}</span>
                    </x-slot:label>
                </x-mary-input>
                <x-mary-input wire:model="form.fax"
                    class="bg-inherit"
                    type="tel">
                    <x-slot:label>
                        <span class="font-semibold text-gray-500">
                            {{ __('fax') }}</span>
                    </x-slot:label>
                </x-mary-input>
                <x-mary-input wire:model="form.whatsapp"
                    class="bg-inherit"
                    type="tel">
                    <x-slot:label>
                        <span class="font-semibold text-gray-500">
                            {{ __('whatsapp') }}</span>
                    </x-slot:label>
                </x-mary-input>
                <x-mary-input wire:model="form.email"
                    class="bg-inherit"
                    type="email">
                    <x-slot:label>
                        <span class="font-semibold text-gray-500">
                            {{ __('email') }}</span>
                    </x-slot:label>
                </x-mary-input>
                <x-mary-select :options="$cities"
                    {{-- icon="c-briefcase" --}}
                    placeholder="{{ __('select_city') }}"
                    placeholder-value="0"
                    wire:model="form.city_id"
                    class="bg-inherit">
                    <x-slot:label>
                        <span class="font-semibold text-gray-500">
                            {{ __('city') }}</span>
                    </x-slot:label>
                </x-mary-select>
            </div>

            <x-mary-header title=""
                subtitle="{{ __('working_hours') }}"
                class="mb-2 font-semibold" />
            <div class="h-64 overflow-auto">
                @foreach ($form->selectedWorkingHours as $weekNumber)
                    <div class="grid items-center grid-cols-3 mb-1 space-x-2">
                        <x-mary-checkbox
                            label="{{ __('week_day_' . $weekNumber['day_of_week']) }}"
                            name="selectedWorkingHour"
                            id="day_{{ $weekNumber['day_of_week'] }}"
                            wire:model.live="form.selectedWorkingHours.{{ $loop->index }}.selected" />
                        <x-mary-datetime class="h-8 bg-white"
                            wire:model="form.selectedWorkingHours.{{ $loop->index }}.opening_time"
                            type="time"
                            :disabled="!$form->selectedWorkingHours[
                                $loop->index
                            ]['selected']">
                            <x-slot:label>
                                <span class="font-semibold text-gray-500">
                                    {{ __('from') }}</span>
                            </x-slot:label>
                        </x-mary-datetime>
                        <x-mary-datetime class="h-8 bg-white"
                            wire:model="form.selectedWorkingHours.{{ $loop->index }}.closing_time"
                            type="time"
                            :disabled="!$form->selectedWorkingHours[
                                $loop->index
                            ]['selected']">
                            <x-slot:label>
                                <span class="font-semibold text-gray-500">
                                    {{ __('to') }}</span>
                            </x-slot:label>
                        </x-mary-datetime>
                    </div>
                @endforeach
            </div>
            @if ($editMode)
                @can('update', $form->medicalCenter)
                    <x-slot:actions>
                        <x-mary-button label="Confirm"
                            type="submit"
                            spinner="save"
                            class="w-full mt-3 btn-primary" />
                    </x-slot:actions>
                @endcan
            @else
                @can('create', MedicalCenter::class)
                    <x-slot:actions>
                        <x-mary-button label="Confirm"
                            type="submit"
                            spinner="save"
                            class="w-full mt-3 btn-primary" />
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
