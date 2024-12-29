@php use App\Models\User; @endphp
<div class="bg-white p-6 w-full">
    <x-mary-header title="{{__('doctor') .' / ' . $this->form->name}}"
                   separator progress-indicator>
        {{--        <x-slot:middle class="!justify-end ">--}}
        {{--            <x-mary-input placeholder="{{__('search')}}"--}}
        {{--                          wire:model.blur="searchWord"--}}
        {{--                          wire:keydown.enter="users"--}}
        {{--                          class="border-primary focus:outline-primary">--}}
        {{--            </x-mary-input>--}}
        {{--        </x-slot:middle>--}}
        <x-slot name="actions">
            <div class="flex gap-1 flex-wrap">
                <x-mary-button
                        class="btn-warning w-36"
                        @click="">
                    {{__('change_password')}}
                </x-mary-button>
                <x-mary-button
                        class="btn-error w-36"
                        @click="">
                    {{__('delete')}}
                </x-mary-button>
                @if($editMode)
                    <div class="w-36 flex items-center justify-evenly">
                        <x-mary-button
                                class="btn btn-circle btn-error"
                                icon="c-x-mark"
                                @click="$wire.setEditMode(false)"
                        />
                        <x-mary-button
                                class="btn btn-circle btn-success"
                                icon="c-check"
                                @click="$wire.save"
                        />
                    </div>
                    {{--                    <x-mary-button--}}
                    {{--                            class="btn-primary w-36"--}}
                    {{--                            @click="$wire.save">--}}
                    {{--                        {{__('save')}}--}}
                    {{--                    </x-mary-button>--}}
                @else
                    <x-mary-button
                            class="btn-info  w-36"
                            @click="$wire.setEditMode(true)">
                        {{__('edit')}}
                    </x-mary-button>
                @endif
            </div>
            {{--            @can('create', User::class)--}}
            {{--                <x-mary-button--}}
            {{--                        class="btn-warning w-36"--}}
            {{--                        @click="">--}}
            {{--                    {{__('change_password')}}--}}
            {{--                </x-mary-button>--}}
            {{--                <x-mary-button--}}
            {{--                        class="btn-error w-36"--}}
            {{--                        @click="">--}}
            {{--                    {{__('delete')}}--}}
            {{--                </x-mary-button>--}}
            {{--                <x-mary-button--}}
            {{--                        class="btn-info  w-36"--}}
            {{--                        @click="">--}}
            {{--                    {{__('edit')}}--}}
            {{--                </x-mary-button>--}}
            {{--            @endcan--}}

        </x-slot>
    </x-mary-header>


    <x-mary-form wire:submit="save" no-separator>
        <div class="grid grid-cols-1 lg:grid-cols-2 lg:space-x-4 space-y-8">
            <div>
                <div
                        class="grid-cols-2 grid space-x-1 gap-3"
                >
                    <x-mary-input
                            label="{{__('name')}}"
                            wire:model="form.name"
                            class="!bg-inherit"
                            type="text"
                            :disabled="!$this->editMode"
                    />
                    <x-mary-input
                            label="{{__('username')}}"
                            wire:model="form.username"
                            class="!bg-inherit"
                            type="text"
                            :disabled="!$this->editMode"
                    />
                    <x-mary-input
                            label="{{__('email')}}"
                            wire:model="form.email"
                            class="!bg-inherit"
                            type="email"
                            :disabled="!$this->editMode"
                    />
                    {{--                    @if(!$editMode)--}}
                    {{--                        <x-mary-input--}}
                    {{--                                label="{{__('password')}}"--}}
                    {{--                                wire:model="form.password"--}}
                    {{--                                class="!bg-inherit"--}}
                    {{--                                type="password"--}}
                    {{--                                --}}{{--                        :disabled="$this->disableInputsWhenEditMode()"--}}
                    {{--                        />--}}
                    {{--                    @endif--}}
                    <x-mary-input
                            label="{{__('phone')}}"
                            wire:model="form.phone"
                            class="!bg-inherit"
                            type="tel"
                            :disabled="!$this->editMode"
                    />
                    {{--            <x-mary-select--}}
                    {{--                    :options="$roles"--}}
                    {{--                    icon="c-briefcase"--}}
                    {{--                    placeholder="{{__('select_role')}}"--}}
                    {{--                    placeholder-value="0"--}}
                    {{--                    wire:model="form.role_id"--}}
                    {{--                    class=""--}}
                    {{--                    --}}{{--                    :disabled="$this->disableInputsWhenEditMode()"--}}
                    {{--            >--}}
                    {{--                <x-slot:label>--}}
                    {{--                        <span class="text-gray-500 font-semibold">--}}
                    {{--                            {{__('role')}}</span>--}}
                    {{--                </x-slot:label>--}}
                    {{--            </x-mary-select>--}}

                </div>
                {{--                <x-mary-header title="{{__('select_medical_centers')}}"--}}
                {{--                               subtitle="{{__('select_multiple')}}"--}}
                {{--                               size="text-lg" class="mb-2"--}}
                {{--                />--}}
                {{--                <ul class="max-h-64 overflow-auto grid grid-cols-3  space-x-2">--}}
                {{--                    @foreach($medicalCenters as $medicalCenter)--}}
                {{--                        <li>--}}
                {{--                            <input type="checkbox"--}}
                {{--                                   id="{{$medicalCenter->id}}"--}}
                {{--                                   name="medicalCenters" class="hidden peer"--}}
                {{--                                   wire:model.live.number="form.medicalCenters"--}}
                {{--                                   value="{{ $medicalCenter->id }}"--}}
                {{--                                    --}}{{--                           @if($this->disableInputsWhenEditMode())--}}
                {{--                                    --}}{{--                               disabled--}}
                {{--                                    --}}{{--                            @endif--}}
                {{--                            />--}}
                {{--                            <label for="{{$medicalCenter->id}}"--}}
                {{--                                   class="inline-flex--}}
                {{--                            items-center justify-center w-full p-1--}}
                {{--                            rounded-2xl py-2--}}
                {{--                            border border-gray-300  cursor-pointer--}}
                {{--                            peer-checked:border-primary peer-checked:text-primary--}}
                {{--                            peer-checked:bg-accent--}}
                {{--                            hover:text-primary hover:bg-accent ">--}}
                {{--                                <p class="text-center text-sm">{{$medicalCenter->name}}</p>--}}
                {{--                            </label>--}}
                {{--                        </li>--}}
                {{--                    @endforeach--}}
                {{--                </ul>--}}
            </div>
            {{--            Medical centers --}}
            <div class="lg:col-span-2">
                <x-mary-header title="{{__('medical_centers')}}"
                               size="text-lg" class="mb-2"
                >

                    <x-slot name="actions">
                        <x-mary-button
                                icon="o-plus"
                                class="btn-sm btn-circle btn-primary"
                                @click=""/>
                    </x-slot>
                </x-mary-header>
                {{--                <x-mary-tabs wire:model="selectedTab"--}}
                {{--                             active-class="bg-primary rounded text-white"--}}
                {{--                             label-class="font-semibold"--}}
                {{--                             label-div-class="bg-primary/5 p-2 rounded"--}}
                {{--                >--}}
                {{--                    @foreach($form->user->medicalCenters as $medicalCenter)--}}
                {{--                        <x-mary-tab name="{{$medicalCenter->name}}-tab"--}}
                {{--                                    label="{{$medicalCenter->name}}"--}}
                {{--                                    wire:click="setMedicalCenter({{ $medicalCenter->id }})"--}}
                {{--                        >--}}
                {{--                            <div>--}}
                {{--                                @foreach($scheduleForm->selectedWorkingHours as $weekNumber)--}}
                {{--                                    <div class="grid grid-cols-3--}}
                {{--                                        space-x-2 mb-1 items-center w-full--}}
                {{--                                        max-w-md"--}}
                {{--                                    >--}}
                {{--                                        <x-mary-checkbox--}}
                {{--                                                label="{{ __('week_day_' . $weekNumber['day_of_week']) }}"--}}
                {{--                                                name="selectedWorkingHour"--}}
                {{--                                                id="day_{{ $weekNumber['day_of_week'] }}"--}}
                {{--                                                wire:model.live="scheduleForm.selectedWorkingHours.{{$loop->index }}.selected"--}}
                {{--                                                :disabled="!$this->checkMCWorkingHours($medicalCenter, $weekNumber['day_of_week'])"--}}
                {{--                                        />--}}
                {{--                                        <x-mary-datetime--}}
                {{--                                                class="h-8 bg-white"--}}
                {{--                                                wire:model="scheduleForm.selectedWorkingHours.{{$loop->index }}.start_time"--}}
                {{--                                                type="time"--}}
                {{--                                                :disabled="!$scheduleForm->selectedWorkingHours[$loop->index]['selected']"--}}
                {{--                                                min="22:00"--}}
                {{--                                                --}}{{--                                                :min="$this->scheduleForm->selectedWorkingHours[$loop->index]['min']"--}}
                {{--                                                --}}{{--                                                :max="$this->scheduleForm->selectedWorkingHours[$loop->index]['max']"--}}
                {{--                                        >--}}
                {{--                                            <x-slot:label>--}}
                {{--                                    <span class="text-gray-500 font-semibold">--}}
                {{--                                        {{__('from')}}</span>--}}
                {{--                                            </x-slot:label>--}}
                {{--                                        </x-mary-datetime>--}}
                {{--                                        <x-mary-datetime--}}
                {{--                                                class="h-8 bg-white"--}}
                {{--                                                wire:model="scheduleForm.selectedWorkingHours.{{$loop->index }}.end_time"--}}
                {{--                                                type="time"--}}
                {{--                                                :disabled="!$scheduleForm->selectedWorkingHours[$loop->index]['selected']">--}}
                {{--                                            <x-slot:label>--}}
                {{--                                    <span class="text-gray-500 font-semibold">--}}
                {{--                                        {{__('to')}}</span>--}}
                {{--                                            </x-slot:label>--}}
                {{--                                        </x-mary-datetime>--}}
                {{--                                    </div>--}}
                {{--                                @endforeach--}}
                {{--                            </div>--}}
                {{--                        </x-mary-tab>--}}
                {{--                    @endforeach--}}

                {{--                </x-mary-tabs>--}}


                <ul class="flex flex-wrap text-sm font-medium text-center
                text-gray-500 border-b border-primary bg-accent p-2">

                    @foreach($form->user->medicalCenters as $medicalCenter)
                        <li class="me-2"
                            wire:click="setMedicalCenter({{json_encode
                            ($medicalCenter) }})">
                            <p
                                    class="inline-block p-2
                               {{ $medicalCenter->id ===
                               $selectedMedicalCenter?->id ?
                               'text-white bg-primary' : '' }}
                           rounded hover:text-white hover:bg-primary cursor-pointer"
                            >{{$medicalCenter->name}}</p>
                        </li>
                    @endforeach
                </ul>
                @if($selectedMedicalCenter )
                    <div>
                        @foreach($scheduleForm->selectedWorkingHours as $weekNumber)
                            <div class="grid grid-cols-3
                                                                space-x-2 mb-1 items-center w-full
                                                                max-w-md"
                            >
                                <x-mary-checkbox
                                        label="{{ __('week_day_' . $weekNumber['day_of_week']) }}"
                                        name="selectedWorkingHour"
                                        id="day_{{ $weekNumber['day_of_week'] }}"
                                        wire:model.live="scheduleForm.selectedWorkingHours.{{$loop->index }}.selected"
                                        :disabled="!$this->checkMCWorkingHours( $weekNumber['day_of_week'])"
                                />
                                <x-mary-datetime
                                        class="h-8 bg-white"
                                        wire:model="scheduleForm.selectedWorkingHours.{{$loop->index }}.start_time"
                                        type="time"
                                        :disabled="!$scheduleForm->selectedWorkingHours[$loop->index]['selected']"
                                        :min="$this->scheduleForm->selectedWorkingHours[$loop->index]['min']"
                                        :max="$this->scheduleForm->selectedWorkingHours[$loop->index]['max']"
                                >
                                    <x-slot:label>
                                                            <span class="text-gray-500 font-semibold">
                                                                {{__('from')}}</span>
                                    </x-slot:label>
                                </x-mary-datetime>
                                <x-mary-datetime
                                        class="h-8 bg-white"
                                        wire:model="scheduleForm.selectedWorkingHours.{{$loop->index }}.end_time"
                                        type="time"
                                        :min="$this->scheduleForm->selectedWorkingHours[$loop->index]['min']"
                                        :max="$this->scheduleForm->selectedWorkingHours[$loop->index]['max']"
                                        :disabled="!$scheduleForm->selectedWorkingHours[$loop->index]['selected']">
                                    <x-slot:label>
                                                            <span class="text-gray-500 font-semibold">
                                                                {{__('to')}}</span>
                                    </x-slot:label>
                                </x-mary-datetime>
                            </div>
                        @endforeach

                    </div>
                @endif

            </div>
        </div>


        @if($editMode)
            @can('update', $form->user)
                <x-slot:actions>
                    {{--                    <x-mary-button label="Confirm" type="submit"--}}
                    {{--                                   spinner="save"--}}
                    {{--                                   class="btn-primary w-full--}}
                    {{--                                       mt-3"/>--}}
                </x-slot:actions>
            @endcan
        @else
            @can('create', User::class)
                <x-slot:actions>
                    {{--                    <x-mary-button label="Confirm" type="submit"--}}
                    {{--                                   spinner="save"--}}
                    {{--                                   class="bg-primary w-full text-white mt-3"/>--}}
                </x-slot:actions>
            @endcan
        @endif
    </x-mary-form>

</div>
