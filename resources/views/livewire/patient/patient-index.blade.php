@php use App\Models\Patient; @endphp
<div class="w-full p-6 bg-white">
    {{--    Header --}}
    <x-mary-header title="{{ __('patients') }}"
        subtitle="{{ __('all_patients') }}"
        separator
        progress-indicator>
        <x-slot:middle
            class="!justify-end ">
            <x-mary-input placeholder="{{ __('search') }}"
                wire:model.blur="searchWord"
                wire:keydown.enter="patients"
                class="">
            </x-mary-input>
        </x-slot:middle>
        <x-slot name="actions">
            <x-mary-button icon="o-funnel"
                class="relative  btn-circle"
                @click="$wire.showFilterDrawer = true">
                @if ($this->filtersCount() > 0)
                    <x-mary-badge value="{{ $this->filtersCount() }}"
                        class="absolute badge-warning -right-2 -top-2" />
                @endif
            </x-mary-button>
            @can('create', Patient::class)
                <x-mary-button icon="o-plus"
                    class="btn-primary btn-circle "
                    link="{{ route('patient.create') }}">
                </x-mary-button>
            @endcan

        </x-slot>
    </x-mary-header>
    {{--    Filter Drawer --}}
    <x-mary-drawer wire:model="showFilterDrawer"
        wire:ignore.self
        class="w-11/12 lg:w-1/3 "
        title="{{ __('filter') }}"
        with-close-button
        right
        separator>
        <div class="space-y-2">
            <x-mary-input placeholder="{{ __('search') }}"
                wire:model.blur="searchWord" />

            {{--            <x-mary-select label="{{__('roles')}}" --}}
            {{--                           :options="$roles" --}}
            {{--                           icon="c-briefcase" --}}
            {{--                           placeholder="{{__('select_role')}}" --}}
            {{--                           placeholder-value="0" --}}
            {{--                           wire:model.live="roleId" --}}
            {{--            /> --}}



            {{--            <x-mary-select label="{{__('medical_centers')}}" --}}
            {{--                           :options="$medicalCenters" --}}
            {{--                           icon="c-briefcase" --}}
            {{--                           placeholder="{{__('select_medical_center')}}" --}}
            {{--                           placeholder-value="0" --}}
            {{--                           wire:model.live="medicalCenterId" --}}
            {{--            /> --}}
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
                    class="btn-warning " />
            @endif
            <x-mary-button label="{{ __('done') }}"
                @click="$wire.showFilterDrawer = false"
                class="btn-primary " />
        </x-slot:actions>
    </x-mary-drawer>
    {{-- Data Table --}}
    <x-mary-table :headers="$headers"
        :rows="$this->patients()"
        with-pagination
        per-page="perPage"
        :sort-by="$sortBy"
        :row-decoration="$this->getRowDecoration()"
        class="[&_th>*]:!text-black [&_th>*]:!inline-flex
                  [&_th>*]:!font-bold "
        :per-page-values="$perPageOptions"
        show-empty-text
        empty-text="{{ __('no_data_found') }}">
        @scope('header_id', $header)
            <p class="font-bold text-black ">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('header_full_name', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('header_gender', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('header_id_number', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('header_birth_date', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('actions', $user)
            <div class="flex space-x-1">

                @if ($this->showArchived)
                    @can('restore', $user)
                        <x-mary-button icon="c-arrow-up-tray"
                            wire:click="changeShowConfirmModal(true, 'are_you_sure_restore',false,{{ $user['id'] }}) "
                            class=" btn-sm btn-circle btn btn-primary
                                   btn-outline bg-accent hover:!text-white" />
                    @endcan
                @else
                    @can('delete', $user)
                        <x-mary-button icon="s-trash"
                            wire:click="changeShowConfirmModal(true, 'are_you_sure_delete', true, {{ $user['id'] }}) "
                            class=" btn btn-sm btn-circle
                                   btn-error
                                   btn-outline hover:!text-white bg-accent
                       " />
                    @endcan

                    @canany(['update', 'view'], $user)
                        <x-mary-button icon="s-pencil"
                            wire:click="edit({{ $user['id'] }})"
                            class=" btn-sm btn-circle btn-info
                                   btn-outline hover:!text-white bg-accent" />
                        <x-mary-button icon="c-eye"
                            link="{{ route('patient.view', $user['id']) }}"
                            class=" btn-sm btn-circle
                                   btn-primary
                                   btn-outline hover:!text-white bg-accent " />
                    @endcanany
                @endif
            </div>
        @endscope

    </x-mary-table>
    {{--    Add modal --}}
    <x-mary-modal wire:model="addModal"
        subtitle=""
        box-class="bg-white border-2 border-primary"
        persistent>


        <x-mary-header
            title="{{ $editMode ? __('edit_patient') : __('add_patient') }}"
            subtitle="{{ $editMode ? '' : __('add_patient_subtitle') }}"
            size="text-2xl"
            class="mb-5">
            <x-slot:actions>
                <x-mary-button icon="o-x-mark"
                    @click="$wire.hideAddModal" />
            </x-slot:actions>
        </x-mary-header>

        <x-mary-form wire:submit="save"
            no-separator>
            <div class="grid grid-cols-2 gap-3 space-x-1">
                <x-mary-input wire:model="form.full_name"
                    class="bg-inherit"
                    type="text"
                    :disabled="$this->disableInputsWhenEditMode()">
                    <x-slot:label>
                        <span class="font-semibold text-gray-500">
                            {{ __('full_name') }}</span>
                    </x-slot:label>
                </x-mary-input>
                <div class="flex flex-col justify-evenly">
                    <p class="font-semibold text-gray-500">{{ __('duration') }}
                    </p>
                    <ul class="grid w-full grid-cols-2 gap-1">
                        @foreach (\App\Enums\GenderEnum::values() as $value)
                            <li>
                                <input type="radio"
                                    id="{{ $value }}"
                                    name="duration"
                                    class="hidden peer"
                                    wire:model.live="form.gender"
                                    value="{{ $value }}" />
                                <label for="{{ $value }}"
                                    class="inline-flex items-center justify-center w-full p-1 py-2 border cursor-pointer rounded-2xl border-primary peer-checked:border-primary peer-checked:text-primary peer-checked:bg-accent hover:text-primary hover:bg-accent ">
                                    <p class="text-sm text-center">
                                        {{ \App\Enums\GenderEnum::labels()[$value] }}
                                    </p>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </div>


                {{--                <x-mary-input --}}
                {{--                        wire:model="form.gender" --}}
                {{--                        class="" --}}
                {{--                        type="text" --}}
                {{--                        :disabled="$this->disableInputsWhenEditMode()" --}}
                {{--                > --}}
                {{--                    <x-slot:label> --}}
                {{--                                        <span class="font-semibold text-gray-500"> --}}
                {{--                                            {{__('gender')}}</span> --}}
                {{--                    </x-slot:label> --}}
                {{--                </x-mary-input> --}}
                <x-mary-input wire:model="form.birth_date"
                    class="bg-inherit"
                    type="date"
                    :disabled="$this->disableInputsWhenEditMode()">
                    <x-slot:label>
                        <span class="font-semibold text-gray-500">
                            {{ __('birth_date') }}</span>
                    </x-slot:label>
                </x-mary-input>
                <x-mary-input wire:model="form.id_number"
                    class="bg-inherit"
                    type="text"
                    :disabled="$this->disableInputsWhenEditMode()">
                    <x-slot:label>
                        <span class="font-semibold text-gray-500">
                            {{ __('id_number') }}</span>
                    </x-slot:label>
                </x-mary-input>
                <x-mary-input wire:model="form.guardian_phone"
                    class="bg-inherit"
                    type="tel"
                    :disabled="$this->disableInputsWhenEditMode()">
                    <x-slot:label>
                        <span class="font-semibold text-gray-500">
                            {{ __('guardian_phone') }}</span>
                    </x-slot:label>
                </x-mary-input>
                <x-mary-input wire:model="form.patient_phone"
                    class="bg-inherit"
                    type="tel"
                    :disabled="$this->disableInputsWhenEditMode()">
                    <x-slot:label>
                        <span class="font-semibold text-gray-500">
                            {{ __('patient_phone') }}</span>
                    </x-slot:label>
                </x-mary-input>
            </div>
            <x-mary-select :options="$cities"
                icon="c-map-pin"
                placeholder="{{ __('select_city') }}"
                placeholder-value="0"
                wire:model="form.city_id"
                class="bg-inherit"
                :disabled="$this->disableInputsWhenEditMode()">
                <x-slot:label>
                    <span class="font-semibold text-gray-500">
                        {{ __('city') }}</span>
                </x-slot:label>
            </x-mary-select>
            <x-mary-menu-separator />
            <x-mary-header title=""
                subtitle="{{ __('patient_funds') }}"
                class="mb-2 font-semibold "
                separator>

                <x-slot:middle
                    class="!justify-end ">
                    <x-mary-input
                        placeholder="{{ __('search patient fund') }}"
                        wire:model.live.debounce.300ms="searchPatientFundWord"
                        wire:keydown.enter.prevent="patientFunds"
                        class="!py-0.5 h-8 bg-inherit"
                        type="text">
                    </x-mary-input>
                </x-slot:middle>
            </x-mary-header>
            <div class="h-56 pt-2 overflow-auto pe-2">
                @foreach ($this->patientFunds() as $patientFund)
                    <div
                        class="grid items-center grid-cols-3 mb-1 space-x-2 ">
                        <x-mary-checkbox label="{{ $patientFund['name'] }}"
                            name="patientFunds"
                            id="{{ $patientFund['id'] }}"
                            wire:model.live="form.patientFunds" />
                        <x-mary-input class="h-8 bg-white"
                            wire:model="form.patientFunds.contribution_percentage"
                            type="text"
                            {{--                                :disabled="!$form->selectedWorkingHours[$loop->index]['selected']" --}} />
                        <p>{{ __($patientFund->contribution_type) }}</p>

                    </div>
                @endforeach
            </div>


            @if ($editMode)
                @can('update', $form->patient)
                    <x-slot:actions>
                        <x-mary-button label="{{ __('confirm') }}"
                            type="submit"
                            spinner="save"
                            class="w-full mt-3 btn btn-primary" />
                    </x-slot:actions>
                @endcan
            @else
                @can('create', Patient::class)
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

{{-- --}}
