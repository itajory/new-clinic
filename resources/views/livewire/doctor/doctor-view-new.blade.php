@php
    use App\Models\DoctorSchedule;
@endphp
<div class="w-full p-6 bg-white ">
    <x-mary-header title="{{ __('doctor') . ' / ' . $doctor->name }}"
        separator
        progress-indicator>
        <x-slot name="actions">
        </x-slot>
    </x-mary-header>

    <div class="w-full ">
        <div class="md:flex">
            <ul
                class="mb-4 space-y-4 text-sm font-medium text-gray-500 flex-column space-y md:me-4 md:mb-0">
                @foreach ($tabs as $key => $tab)
                    <li>
                        <button type="button"
                            class="inline-flex items-center px-4 py-3
                                border-s-4 border-cultured group
                       hover:text-black hover:font-bold w-full hover:border-primary
                         cursor-pointer {{ $activeTab == $key ? ' text-black font-bold border-primary ' : '' }}"
                            wire:click.prevent="setActiveTab('{{ $key }}')">
                            <svg class="w-4 h-4 me-2  group-hover:text-black  {{ $activeTab == $key ? ' text-black ' : ' text-gray-500 ' }}"
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round">
                                @php echo $tab['icon']; @endphp
                            </svg>
                            {{ __($tab['title']) }}
                        </button>
                    </li>
                    {{--                    <li> --}}
                    {{--                        <button --}}
                    {{--                                type="button" --}}
                    {{--                                class="inline-flex items-center px-4 py-3 --}}
                    {{--                       hover:text-gray-900hover:bg-accent w-full --}}
                    {{--                         cursor-pointer {{ $activeTab == $key ? --}}
                    {{--                         ' text-white bg-primary ' : ' bg-cultured ' }}" --}}
                    {{--                                wire:click.prevent="setActiveTab('{{$key}}')" --}}
                    {{--                        > --}}
                    {{--                            <svg class="w-4 h-4 me-2  {{ $activeTab == $key ? --}}
                    {{--                             ' text-white ' : ' text-gray-500 ' }}" --}}
                    {{--                                 xmlns="http://www.w3.org/2000/svg" width="24" --}}
                    {{--                                 height="24" viewBox="0 0 24 24" fill="none" --}}
                    {{--                                 stroke="currentColor" stroke-width="2" --}}
                    {{--                                 stroke-linecap="round" stroke-linejoin="round"> --}}
                    {{--                                @php echo $tab['icon']; @endphp --}}
                    {{--                            </svg> --}}
                    {{--                            {{__($tab['title'])}} --}}
                    {{--                        </button> --}}
                    {{--                    </li> --}}
                @endforeach
            </ul>
            <div class="w-full border-2 border-cultured ">
                @if ($activeTab == 'personal_info')
                    <div
                        class="grid grid-cols-1 gap-2 p-4 md:grid-cols-12 md:gap-6 ">

                        {{--                        <div class="font-semibold md:col-span-2"> --}}
                        {{--                            {{__('name')}}</div> --}}
                        {{--                        <div class="md:col-span-10">{{$doctor->name}}</div> --}}


                        <div class="font-semibold md:col-span-2">
                            {{ __('username') }}</div>
                        <div class="md:col-span-10">{{ $doctor->username }}
                        </div>


                        <div class="font-semibold md:col-span-2">
                            {{ __('email') }}</div>
                        <div class="md:col-span-10">{{ $doctor->email }}</div>


                        <div class="font-semibold md:col-span-2">
                            {{ __('phone') }}</div>
                        <div class="md:col-span-10">{{ $doctor->phone }}</div>
                        <div class="font-semibold md:col-span-2">
                            {{ __('treatment') }}</div>
                        <div class="md:col-span-10">
                            {{ $doctor->treatment->name }}</div>

                    </div>
                @elseif ($activeTab == 'appointments')
                    <livewire:doctor.doctor-appointment :$doctor
                        :key="'' . now()" />
                @elseif($activeTab == 'working_hours')
                    <div
                        class="relative flex flex-wrap items-start justify-start h-full gap-2 p-4">
                        @can('create', DoctorSchedule::class)
                            <div class="absolute z-10 bottom-3 end-3 mymenu">

                                <x-mary-dropdown class="bg-accent">
                                    <x-slot:trigger>
                                        <x-mary-button icon="m-plus"
                                            class="btn-circle btn-outline btn-primary" />
                                    </x-slot:trigger>
                                    <x-mary-menu-item
                                        title="{{ __('add day to schedule') }}"
                                        wire:click="changeAddDayToScheduleModalState(true )" />
                                    {{-- <x-mary-menu-item
            title="{{ __('add medical center') }}"
             /> --}}

                                </x-mary-dropdown>
                            </div>
                        @endcan


                        {{--                        @foreach ($doctor->medicalCenters as $medicalCenter) --}}
                        {{--                            <div class="w-full p-4 bg-cultured md:w-1/2"> --}}
                        {{--                                <div class="text-lg font-semibold">{{$medicalCenter->name}}</div> --}}
                        {{--                                <div class="grid grid-cols-1 gap-2 md:grid-cols-2"> --}}
                        {{--                                    <div class="font-semibold">{{__('day')}}</div> --}}
                        {{--                                    <div class="font-semibold">{{__('time')}}</div> --}}
                        {{--                                    @foreach ($medicalCenter->workingHours as $workingHour) --}}
                        {{--                                        <div class="flex items --}}
                        {{--                                        -center justify-between"> --}}
                        {{--                                            <div>{{$workingHour->day_of_week}}</div> --}}
                        {{--                                            <div>{{$workingHour->opening_time}} --}}
                        {{--                                                - --}}
                        {{--                                                {{$workingHour->closing_time}}</div> --}}
                        {{--                                        </div> --}}

                        {{--                                    @endforeach --}}

                        {{--                                </div> --}}
                        {{--                            </div> --}}

                        {{--                        @endforeach --}}
                        <ul
                            class="flex flex-wrap w-full p-2 overflow-auto text-sm font-medium text-center text-gray-500 border-b border-primary bg-accent">
                            @foreach ($doctor->medicalCenters as $medicalCenter)
                                <li class="me-2 "
                                    wire:click="setMedicalCenter({{ json_encode($medicalCenter) }})">
                                    <p
                                        class="inline-block p-2 {{ $medicalCenter->id === $selectedMedicalCenter?->id
                                            ? 'text-white bg-primary'
                                            : '' }} rounded
                                     hover:text-white hover:bg-primary cursor-pointer min-w-32">
                                        {{ $medicalCenter->name }}</p>
                                </li>
                            @endforeach
                        </ul>
                        @if ($selectedMedicalCenter)
                            <div class="w-full">
                                <x-mary-table :headers="$workingHoursHeaders"
                                    :rows="$this->getDoctorMedicalCenterSchedule()"
                                    :row-decoration="$this->getRowDecoration()"
                                    show-empty-text
                                    empty-text="{{ __('no_data_found') }}">


                                    @scope('header_day_of_week', $header)
                                        <p class="font-bold text-black">
                                            {{ $header['label'] }}
                                        </p>
                                    @endscope
                                    @scope('header_start_time', $header)
                                        <p class="font-bold text-black">
                                            {{ $header['label'] }}
                                        </p>
                                    @endscope
                                    @scope('header_end_time', $header)
                                        <p class="font-bold text-black">
                                            {{ $header['label'] }}
                                        </p>
                                    @endscope

                                    @scope('header_actions', $header)
                                        <p class="font-bold text-black">
                                            {{ $header['label'] }}
                                        </p>
                                    @endscope

                                    @scope('cell_day_of_week', $workingHours)
                                        {{ __('week_day_' . $workingHours['day_of_week']) }}
                                    @endscope
                                    @scope('cell_start_time', $workingHours)
                                        {{ $this->formatTime($workingHours['start_time']) }}
                                    @endscope
                                    @scope('cell_end_time', $workingHours)
                                        {{ $this->formatTime($workingHours['end_time']) }}
                                    @endscope


                                    @scope('cell_actions', $workingHours)
                                        <div class="flex space-x-1">

                                            @can('delete', $workingHours)
                                                <x-mary-button icon="s-trash"
                                                    wire:confirm="{{ __('are_you_sure_delete') }}"
                                                    class="btn btn-sm btn-circle btn-error btn-outline hover:!text-white bg-accent" />
                                            @endcan
                                            @can('update', $workingHours)
                                                <x-mary-button icon="s-pencil"
                                                    wire:click="editWorkingHour({{ $workingHours['id'] }})"
                                                    class="btn-sm
                                                           btn-circle btn-info btn-outline hover:!text-white bg-accent" />
                                            @endcan

                                        </div>
                                    @endscope

                                </x-mary-table>
                            </div>

                        @endif

                    </div>
                @endif
            </div>

        </div>
    </div>


    {{--    modals --}}
    {{--    add / edit working hour day  --}}
    @if ($activeTab == 'working_hours')
        <x-mary-modal wire:model="showEditWorkingHourModal"
            subtitle=""
            box-class="border-2 border-primary"
            persistent>
            <x-mary-header
                title="
                   {{ ($selectedSchedule ? __('Edit') : __('Add')) .
                       ' ' .
                       __('week_day_' . $scheduleForm->day_of_week) }}"
                size="text-2xl"
                class="mb-5">
                <x-slot:actions>
                    <x-mary-button icon="o-x-mark"
                        wire:click.prevent="changeWorkingHourModalState(false)" />
                </x-slot:actions>
            </x-mary-header>
            <x-mary-form wire:submit="saveWorkingHours"
                no-separator>
                <div class="grid grid-cols-2 gap-3 mt-6">

                    <x-mary-input label="{{ __('start_time') }}"
                        wire:model="scheduleForm.start_time"
                        type="time"
                        pattern="\d{2}:\d{2}:\d{2}"
                        class="" />

                    <x-mary-input label="{{ __('end_time') }}"
                        wire:model="scheduleForm.end_time"
                        type="time"
                        pattern="\d{2}:\d{2}:\d{2}"
                        class="" />

                    <div
                        class="flex items-center justify-start col-span-2 text-sm text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="w-4 me-3">
                            <circle cx="12"
                                cy="12"
                                r="10" />
                            <path d="M12 16v-4" />
                            <path d="M12 8h.01" />
                        </svg>
                        {{ $scheduleForm->fromToHint }}
                    </div>
                </div>


                <x-slot:actions>
                    <x-mary-button label="Confirm"
                        type="submit"
                        spinner="saveWorkingHours"
                        class="w-full mt-3 btn btn-primary" />
                </x-slot:actions>
            </x-mary-form>

        </x-mary-modal>

        {{--        select day to add to doctor schedule --}}
        <x-mary-modal wire:model="showAddDayToScheduleModal"
            subtitle=""
            box-class="border-2 border-primary"
            persistent>
            <x-mary-header title="{{ __('add day to schedule') }}"
                size="text-2xl"
                class="mb-5">
                <x-slot:actions>
                    <x-mary-button icon="o-x-mark"
                        wire:click.prevent="changeAddDayToScheduleModalState(false)" />
                </x-slot:actions>
            </x-mary-header>
            <x-mary-form wire:submit="saveWorkingHours"
                no-separator>
                <ul class="grid grid-cols-2 gap-3 mt-6">
                    @foreach ($this->availableDaysToAddToSchedule() as $day)
                        <li class="m-1"
                            wire:click="addNewWorkingHour({{ $day }})">
                            <input type="checkbox"
                                id="{{ $day }}"
                                name="medicalCenters"
                                class="hidden peer"
                                wire:model.live.number="day"
                                value="{{ $day }}" />
                            <label for="{{ $day }}"
                                class="inline-flex items-center justify-center w-full p-1 py-2 border cursor-pointer rounded-2xl border-primary peer-checked:border-primary peer-checked:text-primary peer-checked:bg-accent hover:text-primary hover:bg-accent ">
                                <p class="text-sm text-center">
                                    {{ __('week_day_' . $day) }}
                                </p>
                            </label>
                        </li>
                    @endforeach
                </ul>


                {{--                <x-slot:actions> --}}
                {{--                    --}}{{--                <x-mary-button label="Cancel" @click="$wire.addModal = false"/> --}}
                {{--                    <x-mary-button label="Confirm" type="submit" spinner="save" --}}
                {{--                                   class="w-full mt-3 btn btn-primary"/> --}}
                {{--                </x-slot:actions> --}}
            </x-mary-form>

        </x-mary-modal>

    @endif


</div>
