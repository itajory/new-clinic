<div class="w-full p-6 bg-white rounded-lg shadow-md">
    {{--    Header --}}

    <x-mary-header title="{{ __('Create Patient') }}"
                   subtitle="{{ __('Create a new patient') }}"
                   separator
                   progress-indicator>

        <x-slot name="actions">
            @if ($form->hasData())
            @endif
            <x-mary-button icon="c-x-mark"
                           class=" btn-error"
                           @click="$wire.cancel()">
                {{ __('Cancel') }}
            </x-mary-button>

            <x-mary-button icon="o-check"
                           class="btn-primary "
                           type="submit"
                           @click="$wire.save()">
                {{ __('Save') }}
            </x-mary-button>

        </x-slot>
    </x-mary-header>

    <x-mary-form wire:submit="save"
                 no-separator
                 class="grid max-w-6xl grid-cols-1 space-y-4 ">
        <div x-data="{ selectedCity: '' }">
            <div class="grid gap-4 gir-cols-1 md:grid-cols-2">
                <x-mary-input wire:model="form.full_name"
                              class="bg-inherit"
                              type="text">
                    <x-slot:label>
                        <span class="font-semibold text-gray-500">
                            {{ __('full_name') }}</span>
                    </x-slot:label>
                </x-mary-input>
                <div class="flex flex-col justify-evenly">
                    <p class="font-semibold text-gray-500">{{ __('gender') }}
                    </p>
                    <ul class="grid w-full grid-cols-2 gap-1">
                        @foreach (\App\Enums\GenderEnum::values() as $value)
                            <li>
                                <input type="radio"
                                       id="{{ $value }}"
                                       name="duration"
                                       class="hidden peer"
                                       wire:model.live="form.gender"
                                       value="{{ $value }}"/>
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
                {{--                      --}}
                {{--                > --}}
                {{--                    <x-slot:label> --}}
                {{--                                        <span class="font-semibold text-gray-500"> --}}
                {{--                                            {{__('gender')}}</span> --}}
                {{--                    </x-slot:label> --}}
                {{--                </x-mary-input> --}}
                <x-mary-input wire:model="form.birth_date"
                              class="bg-inherit"
                              type="date">
                    <x-slot:label>
                        <span class="font-semibold text-gray-500">
                            {{ __('birth_date') }}</span>
                    </x-slot:label>
                </x-mary-input>
                <x-mary-input wire:model="form.id_number"
                              class="bg-inherit"
                              type="text">
                    <x-slot:label>
                        <span class="font-semibold text-gray-500">
                            {{ __('id_number') }}</span>
                    </x-slot:label>
                </x-mary-input>
                <x-mary-input wire:model="form.guardian_phone"
                              class="bg-inherit"
                              type="tel">
                    <x-slot:label>
                        <span class="font-semibold text-gray-500">
                            {{ __('guardian_phone') }}</span>
                    </x-slot:label>
                </x-mary-input>
                <x-mary-input wire:model="form.patient_phone"
                              class="bg-inherit"
                              type="tel">
                    <x-slot:label>
                        <span class="font-semibold text-gray-500">
                            {{ __('patient_phone') }}</span>
                    </x-slot:label>
                </x-mary-input>
            </div>
            <x-mary-dropdown
                    placeholder-value="0"
                    wire:model="form.city_id"
                    class="w-100 mt-5 btn-outline !py-0.5 h-8 min-w-60 border-primary hover:bg-inherit hover:text-gray-500 hover:border-primary text-gray-500 custom-dropdown-bg me-4">

                <x-slot:label>
                <span class="font-semibold text-gray-500">
                   {{$this->form->city_id !== 0 ?
                    $this->form->city_name :
                       __('select_city')
                     }}
                </span>
                </x-slot:label>

                <x-mary-menu-item wire:click.stop="" class="bg-transparent">
                    <x-slot:title>
                        <x-mary-input
                                wire:keydown.enter.prevent="searchCityWord"
                                wire:model.live.debounce.300ms="searchCityWord"
                                class="bg-inherit"
                                type="text"/>
                    </x-slot:title>
                </x-mary-menu-item>

                @foreach ($cities as $city)
                    <x-mary-menu-item title="{{ $city->name }}"
                                      wire:click="selectCity({{ $city }})"
                                      x-on:click="selectedCity =
                                      {{$city->name}})"

                    />
                    <x-mary-menu-separator/>
                @endforeach
            </x-mary-dropdown>

        </div>
        <div>

            <x-mary-header title=""
                           subtitle="{{ __('patient_funds') }}"
                           class="mb-2 font-semibold "
                           separator>

                <x-slot:middle
                        class="!justify-end mymenu">
                    @if (collect($this->form->patientFunds)->isEmpty())
                        <x-mary-dropdown
                                label="{{ __('select patient fund') }}"
                                class="btn-outline !py-0.5 h-8
                                     bg-white min-w-60 border-primary
                                     hover:bg-inherit hover:text-gray-500
                                     hover:border-primary text-gray-500
                                     custom-dropdown-bg">
                            <x-mary-menu-item wire:click.stop=""
                                              class="bg-transparent">
                                <x-slot:title>
                                    <x-mary-input
                                            wire:keydown.enter.prevent="patientFunds"
                                            wire:model.live.debounce.300ms="searchPatientFundWord"
                                            class="bg-inherit"
                                            type="text"/>
                                </x-slot:title>
                            </x-mary-menu-item>
                            @foreach ($this->patientFunds() as $patientFund)
                                <x-mary-menu-item
                                        title="{{ $patientFund->name }}"
                                        wire:click="addPatientFund({{ $patientFund }})"/>
                                <x-mary-menu-separator/>
                            @endforeach

                        </x-mary-dropdown>
                    @endif
                </x-slot:middle>
            </x-mary-header>
            <div class="space-y-2">
                <div class="grid grid-cols-6 gap-2 font-semibold text-gray-500">
                    <div class="col-span-2">{{ __('patient_fund_name') }}</div>
                    <div class="col-span-2">{{ __('contribution_type') }}</div>
                    <div class="col-span-1">{{ __('value') }}</div>
                    <div class="col-span-1">{{ __('actions') }}</div>
                </div>
                @if (collect($this->form->patientFunds)->isEmpty())
                    <div class="grid grid-cols-6 gap-2 mt-6">
                        <div class="col-span-6 text-center">{{ __('no_data') }}
                        </div>
                    </div>
                @else
                    @foreach ($this->form->patientFunds as $fund)
                        <div class="grid grid-cols-6 gap-2">
                            <div class="col-span-2">
                                {{ is_array($fund['name']) ? implode(', ', $fund['name']) : htmlspecialchars($fund['name']) }}
                            </div>
                            <div class="col-span-2">
                                {{ is_array($fund['contribution_type']) ? implode(', ', $fund['contribution_type']) : htmlspecialchars($fund['contribution_type']) }}
                            </div>
                            <x-mary-input
                                    wire:model="form.patientFunds.{{ $loop->index }}.contribution_percentage"
                                    class="h-8 col-span-1 px-2 py-1 bg-inherit"
                                    type="number"/>
                            <div class="col-span-1">
                                <x-mary-button icon="c-trash"
                                               class="btn-error btn-sm btn-outline"
                                               wire:click="removePatientFund({{ $loop->index }})"/>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>


    </x-mary-form>
</div>
