@php
    function getAppointmentClass($appointment)
    {
        if (!$appointment['isWorkingHours']) {
            return 'bg-gray-300';
        }

        if ($appointment['appointment']) {
            $statusClasses = [
                'reserved' => 'status-reserved',
                'waiting' => 'status-waiting',
                'completed' => 'status-completed',
                'not_attended_with_telling' => 'status-not-attended-telling',
                'not_attended_without_telling' => 'status-not-attended-no-telling'
            ];

            return $statusClasses[$appointment['appointment']->status] ?? 'bg-red-500';
        }

        return (!$appointment['isDoctorWorkingHours'] ?? false) ? 'bg-gray-200' : 'bg-gray-50';
    }
@endphp

<div class="grid w-full grid-cols-12 gap-2" style="margin-inline-start: -30px"
    x-data="{
        showAddEditAppointmentModal: @entangle('showAddEditAppointmentModal'),
        init() {
            document.addEventListener('hide-add-edit-appointment-modal', () => {
                this.showAddEditAppointmentModal = false;
            });
        }
    }">
    <div class="w-full col-span-12 px-2 py-4 bg-white shadow-md rounded-xl lg:col-span-10">
        <x-mary-header title="{{ __('appointments') }}" subtitle="" separator progress-indicator>
            <x-slot name="actions">
                <ul class="flex flex-wrap w-full p-2 overflow-auto text-sm font-medium text-center text-gray-500">
                    @foreach ($medicalCenters as $medicalCenter)
                        <li class="me-2" wire:click="setMedicalCenter({{ $medicalCenter }})">
                            <p class="inline-block p-2 {{ $medicalCenter->id == $selectedMedicalCenter ? 'text-white bg-primary' : '' }} rounded hover:text-white hover:bg-primary cursor-pointer min-w-32">
                                {{ $medicalCenter->name }}
                            </p>
                        </li>
                    @endforeach
                </ul>
            </x-slot>
        </x-mary-header>

        <div class="flex items-center mb-4 mt-0">
            <input type="checkbox" id="showAllDoctors" wire:model.live="showAllDoctors" class="mr-2">
            <label for="showAllDoctors" class="text-sm text-gray-700">{{ __('Show All Doctors') }}</label>
        </div>

        <div class="table-container">
            <div class="overflow-auto h-[calc(100vh-15rem)]">
                @if (count($generatedAppointments[0]) > 1)
                    <table class="w-full rounded-lg">
                        <thead>
                            <tr class="sticky top-0 z-20 w-full bg-white">
                                @foreach ($generatedAppointments[0] as $doctor)
                                    <th class="sticky top-0 border p-2 text-sm font-semibold text-gray-700 {{ $loop->first ? 'sticky start-0 z-20 bg-white min-w-20 w-20' : 'min-w-40 max-w-40 z-10 bg-white' }}">
                                        {{ $loop->first ? $doctor : $doctor->name }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $renderedAppointments = [];
                            @endphp
                            @foreach (array_slice($generatedAppointments, 1) as $rowIndex => $row)
                                @if ($row[1]['isWorkingHours'])
                                    <tr>
                                        @foreach ($row as $columnIndex => $appointment)
                                            @if ($loop->first)
                                                <th class="sticky z-10 w-20 p-2 text-xs font-semibold text-gray-600 bg-white border start-0 min-w-20">
                                                    {{ $appointment }}
                                                </th>
                                            @else
                                                @php
                                                    $appointmentId = $appointment['appointment']->id ?? null;
                                                @endphp
                                                @if (!isset($renderedAppointments[$columnIndex]) || $renderedAppointments[$columnIndex] !== $appointmentId)
                                                    <td class="border p-2 min-w-40 max-w-40 {{ getAppointmentClass($appointment) }} relative group"
                                                        rowspan="{{ $appointment['appointment'] ? ceil($appointment['appointment']->duration / 15) : 1 }}">
                                                        @if ($appointment['isWorkingHours'])
                                                            @can('create', $appointmentClasss)
                                                                @if ($this->isFutureDateTime($row[0]))
                                                                    <div class="absolute top-0 bottom-0 left-0 right-0 items-center justify-center hidden bg-accent group-hover:flex border-r-primary border-l-primary">
                                                                        <x-mary-icon name="c-plus" class="flex-1 w-8 h-8 cursor-pointer group-hover:text-primary"
                                                                            x-on:click="showAddEditAppointmentModal = true; $wire.changeShowAddEditAppointmentModal(null, '{{ $row[0] }}', {{ $generatedAppointments[0][$columnIndex] }})"/>
                                                                    </div>
                                                                @endif
                                                            @endcan
                                                            @if ($appointment['appointment'])
                                                                <div class="absolute top-0 bottom-0 left-0 right-0 items-center justify-center hidden bg-accent group-hover:flex border-r-primary border-l-primary">
                                                                    <x-mary-icon name="c-pencil" class="flex-1 w-8 h-8 cursor-pointer group-hover:text-warning"
                                                                        x-on:click="showAddEditAppointmentModal = true; $wire.changeShowAddEditAppointmentModal({{ $appointment['appointment'] }})"/>
                                                                </div>
                                                                <div class="flex flex-col items-center justify-center text-sm font-medium text-blue-800">
                                                                    <strong>
                                                                        <snap>{{ $appointment['appointment']->treatment->name }}</snap>
                                                                    </strong>
                                                                    <span class="text-xs">{{ $appointment['appointment']->patient->full_name }}</span>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    @php
                                                        $renderedAppointments[$columnIndex] = $appointmentId;
                                                    @endphp
                                                @endif
                                            @endif
                                        @endforeach
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="flex items-center justify-center w-full h-full">
                        <div class="text-center">{{ __('no_appointments') }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="hidden lg:flex flex-col rounded-xl w-[296px]">
        @php
            $config = [
                'dateFormat' => 'm/d/Y',
                'inline' => true,
            ];
        @endphp
        <x-mary-datepicker label="" wire:model.live="selectedDate" type="date" icon="o-calendar" :config="$config" class="text-primary"/>
    </div>

    <x-mary-modal x-show="showAddEditAppointmentModal" subtitle="" box-class="border-2 border-primary rounded-0" class="appointmentModal max-w-[90%]" persistent>
        <x-mary-header title="{{ isset($selectedAppointment) && $selectedAppointment ? __('edit_appointment') : __('add_appointment') }}" subtitle="" size="text-2xl" class="mb-1" separator progress-indicator>
            @if (!isset($selectedAppointment) || !$selectedAppointment)
                <x-slot:middle class="!justify-center">
                    <div class="flex flex-col items-center justify-center md:flex-row">
                        <x-mary-dropdown class="btn-outline !py-0.5 h-8 min-w-60 border-primary hover:bg-inherit hover:text-gray-500 hover:border-primary text-gray-500 custom-dropdown-bg me-4">
                            <x-slot:label>{{ isset($selectedPatient) && $selectedPatient ? $selectedPatient->full_name : __('select patient') }}</x-slot:label>
                            <x-mary-menu-item wire:click.stop="" class="bg-transparent">
                                <x-slot:title>
                                    <x-mary-input wire:keydown.enter.prevent="patients" wire:model.live.debounce.300ms="searchPatientWord" class="bg-inherit" type="text"/>
                                </x-slot:title>
                            </x-mary-menu-item>
                            @foreach ($this->patients() as $patient)
                                <x-mary-menu-item title="{{ $patient->full_name }}" wire:click="selectPatient({{ $patient }})"/>
                                <x-mary-menu-separator/>
                            @endforeach
                        </x-mary-dropdown>
                        <div class="mx-4 divider divider-horizontal divider-primary">{{ __('OR') }}</div>
                        @can('create', $patientClass)
                            <x-mary-button icon="o-plus" class="btn-primary" wire:click="createNewPatient">
                                {{ __('add_new_patient') }}
                            </x-mary-button>
                        @endcan
                    </div>
                </x-slot:middle>
            @endif
            <x-slot:actions>
                <x-mary-button icon="o-x-mark" x-on:click="showAddEditAppointmentModal = false; $wire.hideAddEditAppointmentModal()" class="btn-error btn-circle"/>
            </x-slot:actions>
        </x-mary-header>

        <div class="grid grid-cols-1 gap-2 lg:grid-cols-3 lg:gap-4">
            @if ((isset($selectedAppointment) && $selectedAppointment || isset($selectedPatient) && $selectedPatient) && !$isNewPatient)
                <div class="col-span-2">
                    <livewire:patient.view-patient :id="$selectedPatient->id" :isNested="true" :key="$selectedPatient->id"/>
                </div>
            @elseif (!isset($selectedPatient) && $isNewPatient && !(isset($selectedAppointment) && $selectedAppointment))
                <div class="col-span-2">
                    <livewire:patient.create-patient :id="0" :isNested="true" :key="0"/>
                </div>
            @endif

            @if (isset($selectedAppointment) && $selectedAppointment && !$isNewPatient)
                <div class="col-span-1 p-4 bg-white rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold">{{ __('appointment_info') }}</h3>
                    <div class="mt-2">
                        <livewire:appointment.view-appointment
                            :selectedPatient="$selectedPatient"
                            :selectedDoctor="$selectedDoctor"
                            :selectedMedicalCenter="$selectedMedicalCenterObj"
                            :selectedTreatment="$selectedDoctorTreatment"
                            :selectedAppointment="$selectedAppointment"
                            :key="$selectedPatient->id"/>
                    </div>
                </div>
            @elseif (isset($selectedPatient) && $selectedPatient && !(isset($selectedAppointment) && $selectedAppointment) && !$isNewPatient)
                <div class="col-span-1 p-4 bg-white rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold">{{ __('appointment_info') }}</h3>
                    <div class="mt-2">
                        <livewire:appointment.create-appointment
                            :selectedPatient="$selectedPatient"
                            :selectedDoctor="$selectedDoctor"
                            :selectedMedicalCenter="$selectedMedicalCenterObj"
                            :selectedTreatment="$selectedDoctorTreatment"
                            :durations="$durations"
                            :selectedTimeSlot="$selectedTimeSlot"
                            :selectedDate="$selectedDate"
                            :key="$selectedPatient->id"/>
                    </div>
                </div>
            @endif
        </div>
    </x-mary-modal>
</div>
