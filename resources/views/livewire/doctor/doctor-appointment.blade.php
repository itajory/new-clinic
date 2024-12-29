@php
    function getAppointmentClass($appointment)
    {
        if (!$appointment['isWorkingHours']) {
            return 'bg-gray-300';
        } elseif ($appointment['appointment']) {
            // Assuming $appointment['appointment'] is an object
            switch ($appointment['appointment']->status) {
                case 'reserved':
                    return 'status-reserved';
                case 'waiting':
                    return 'status-waiting';
                case 'completed':
                    return 'status-completed';
                case 'not_attended_with_telling':
                    return 'status-not-attended-telling';
                case 'not_attended_without_telling':
                    return 'status-not-attended-no-telling';
                default:
                    return 'bg-red-500';
            }
        } elseif (!$appointment['isDoctorWorkingHours']) {
            return 'bg-gray-200';
        } else {
            return 'bg-gray-50';
        }
    }

@endphp

@php
    $config = [
        'dateFormat' => 'm/d/Y',
        // 'inline' => true,
    ];
@endphp

<div x-data="{
    showAddEditAppointmentModal: false,
    showCloseModal: false
}"
    x-on:close-modals.window="showAddEditAppointmentModal = false; showCloseModal = false">
    <div class="flex items-center justify-between w-full p-2">



        <x-mary-datepicker label=""
            wire:model.live="selectedDate"
            type="date"
            icon="o-calendar"
            :config="$config" />

        <ul
            class="flex flex-wrap p-2 overflow-auto text-sm font-medium text-center text-gray-500">
            @foreach ($medicalCenters as $medicalCenter)
                <li class="me-2 "
                    wire:click="setMedicalCenter({{ $medicalCenter }})">
                    <p
                        class="inline-block p-2 {{ $medicalCenter->id == $selectedMedicalCenter ? 'text-white  bg-primary' : '' }} rounded
                             hover:text-white hover:bg-primary cursor-pointer min-w-32">
                        {{ $medicalCenter->name }}</p>
                </li>
            @endforeach
        </ul>
    </div>
    <x-mary-hr />

    @if (count($generatedAppointments[0]) > 1)
        <div class="table-container ">
            <div class="overflow-auto  h-[calc(100vh-20rem)]">
                <table class="w-full rounded-lg ">
                    <thead>
                        <tr class="sticky top-0 z-20 w-full bg-white">
                            @foreach ($generatedAppointments[0] as $doctor)
                                <th
                                    class="sticky top-0  border p-2 text-sm
                        font-semibold
                        text-gray-700 {{ $loop->first ? 'sticky left-0 z-10 bg-white min-w-24 w-24' : ' min-w-40  max-w-40   z-20 bg-white' }}">
                                    @if ($loop->first)
                                        {{ $doctor }}
                                    @else
                                        {{ $doctor }}
                                    @endif
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
                                            <th
                                                class="sticky left-0 z-10 w-24 p-2 text-sm font-semibold text-gray-600 bg-white border min-w-24">
                                                {{ $appointment }}
                                            </th>
                                        @else
                                            @php
                                                $appointmentId =
                                                    $appointment['appointment']
                                                        ->id ?? null;
                                            @endphp
                                            @if (
                                                !isset($renderedAppointments[$columnIndex]) ||
                                                    $renderedAppointments[$columnIndex] !== $appointmentId)
                                                <td class="border p-2 min-w-40 max-w-40 {{ getAppointmentClass($appointment) }} relative group"
                                                    rowspan="{{ $appointment['appointment'] ? ceil($appointment['appointment']->duration / 15) : 1 }}">
                                                    @if ($appointment['isWorkingHours'])
                                                        {{-- @can('create', $appointmentClasss)
                                                    <div
                                                        class="absolute top-0 bottom-0 left-0 right-0 items-center justify-center hidden bg-accent group-hover:flex border-r-primary border-l-primary">
                                                        <x-mary-icon name="c-plus"
                                                            class="flex-1 w-8 h-8 cursor-pointer group-hover:text-primary"
                                                            x-on:click="showAddEditAppointmentModal = true; $wire.changeShowAddEditAppointmentModal(null, '{{ $row[0] }}', {{ $generatedAppointments[0][$columnIndex] }})" />
                                                    </div>
                                                @endcan --}}
                                                        @if ($appointment['appointment'])
                                                            <div
                                                                class="absolute top-0 bottom-0 left-0 right-0 items-center justify-center hidden bg-accent group-hover:flex border-r-primary border-l-primary">
                                                                <x-mary-icon
                                                                    name="c-pencil"
                                                                    class="flex-1 w-8 h-8 cursor-pointer group-hover:text-warning"
                                                                    x-on:click="showAddEditAppointmentModal = true; $wire.changeShowAddEditAppointmentModal({{ $appointment['appointment'] }})" />
                                                            </div>
                                                            <div
                                                                class="flex flex-col items-center justify-center text-sm font-medium text-blue-800">
                                                                <strong>{{ $appointment['appointment']->id }}
                                                                    <snap>
                                                                        {{ $appointment['appointment']->treatment->name }}
                                                                    </snap>
                                                                </strong>
                                                                <span
                                                                    class="text-xs">{{ $appointment['appointment']->patient->full_name }}</span>
                                                            </div>
                                                        @endif
                                                    @endif
                                                </td>
                                                @php
                                                    $renderedAppointments[
                                                        $columnIndex
                                                    ] = $appointmentId;
                                                @endphp
                                            @endif
                                        @endif
                                    @endforeach
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="flex items-center justify-center w-full h-full">
            <div class="text-center">
                {{ __('no_appointments') }}
            </div>
        </div>
    @endif



    {{-- modals --}}
    <x-mary-modal x-show="showAddEditAppointmentModal"
        subtitle=""
        box-class="border-2 border-primary rounded-0"
        class=" appointmentModal"
        persistent>
        <x-mary-header
            title="{{ __('appointment_no') . ' ' . $selectedAppointment?->id }}"
            subtitle=""
            size="text-2xl"
            class="mb-5"
            separator
            progress-indicator>

            <x-slot:actions>
                <x-mary-button icon="o-x-mark"
                    x-on:click="showAddEditAppointmentModal = false; $wire.hideAddEditAppointmentModal()" />
            </x-slot:actions>
        </x-mary-header>
        @if ($selectedAppointment != null)
            <div class="col-span-2">
                <livewire:patient.view-patient :id="$selectedAppointment->patient_id"
                    :isNested="true"
                    :key="$selectedAppointment->patient_id" />

            </div>
        @endif
        @if (
            $selectedAppointment?->status == 'waiting' ||
                $selectedAppointment?->status == 'reserved')
            <div class="sticky z-0 flex justify-end px-5 bottom-20 end-0">
                <x-mary-button class="btn btn-primary"
                    x-on:click="showCloseModal=true">
                    {{ __('Close Appointment') }}
                </x-mary-button>
            </div>
        @endif
    </x-mary-modal>

    {{-- Close modal --}}
    <x-mary-modal x-show="showCloseModal"
        subtitle=""
        box-class="border-2 border-primary rounded-0"
        class=""
        persistent>
        <x-mary-header title="{{ __('Close Appointment') }}"
            subtitle=""
            size="text-2xl"
            class="mb-5"
            separator
            progress-indicator>

            <x-slot:actions>
                <x-mary-button icon="o-x-mark"
                    x-on:click="showCloseModal = false;" />
            </x-slot:actions>
        </x-mary-header>

        <x-mary-form wire:submit="save"
            no-separator>
            <div class="grid items-center justify-center grid-cols-4 gap-2">

                <div class="col-span-3">

                    <x-mary-select label="{{ __('prescription_template') }}"
                        :options="$prescriptions"
                        icon="c-briefcase"
                        placeholder="{{ __('select_prescription_template') }}"
                        placeholder-value="0"
                        wire:model.live.number="form.prescription_template_id"
                        class="border-b-gray-500 focus:border-primary focus:outline-primary focus-within:outline-primary " />
                </div>
                <div class="flex items-center justify-center col-span-1 gap-1">

                    @if ($showApproveOrCancelAdd)
                        <x-mary-button class=" btn-primary btn-sm btn-cicle"
                            icon="c-check"
                            wire:click="addPrescriptionText()" />
                        <x-mary-button class=" btn-error btn-sm btn-cicle"
                            icon="c-x-mark"
                            wire:click="hideApproveOrCancelAdd()" />
                    @endif
                </div>
            </div>

            <x-mary-textarea hint="{{ __('content_hint') }}"
                rows="4"
                label="{{ __('content') }}"
                wire:model="form.description"
                class="" />

            <x-slot:actions>
                <x-mary-button label="{{ __('confirm') }}"
                    type="submit"
                    spinner="save"
                    class="w-full mt-3 btn btn-primary" />
            </x-slot:actions>

        </x-mary-form>


    </x-mary-modal>

</div>
