<div class="grid w-full grid-cols-12 gap-2 "
     style="margin-inline-start: -30px"
     x-data="appointment"
     x-on:appointments-changed.window="afterDataChanged(event)"
     x-on:date-changed.window="setSelectedDate(event)"
     x-on:hide-add-edit-appointment-modal.window="showAddEditAppointmentModal=false"
>
    <div
            class="w-full col-span-12 px-2 py-4 bg-white shadow-md rounded-xl lg:col-span-10">
        <x-mary-header title="{{ __('appointments') }}"
                       subtitle=""
                       separator
                       progress-indicator>
            <x-slot name="actions">
                <ul
                        class="flex flex-wrap w-full p-2 overflow-auto text-sm
                font-medium text-center text-gray-500">
                    <template x-for="(medicalCenter, index) in medicalCenters">
                        <li class="me-2 "
                            @click="selectMedicalCenterA(index)"
                        >
                            <p x-text="medicalCenter.name"
                               :class="[
                                'inline-block p-2 rounded hover:text-white hover:bg-primary cursor-pointer min-w-32',
                                selectedMedicalCenterIndex == index ? 'text-white bg-primary' : ''
                               ]">
                            </p>
                        </li>
                    </template>
                </ul>
            </x-slot>
        </x-mary-header>
        <div class="flex items-center mb-4 mt-0">
            <input type="checkbox" id="showAllDoctors"
                   x-model="showAllDoctors"
                   @change="selectMedicalCenterA()"
                   class="mr-2">
            <label for="showAllDoctors"
                   class="text-sm text-gray-700">{{ __('Show All Doctors') }}</label>
        </div>
        <div class="table-container">
            <div class="overflow-auto  h-[calc(100vh-15rem)]">
                <template x-if="doctors.length > 0">
                    <table class="w-full rounded-lg ">
                        <thead>
                        <tr class="sticky top-0 z-20 w-full bg-white">
                            <th
                                    class="sticky top-0 border p-2
                                        text-sm font-semibold text-gray-700
                                         start-0 z-20 bg-white min-w-20 w-20 ">
                                Time
                            </th>
                            <template x-for="(doctor, index) in doctors"
                                      :key="doctor.id">
                                <th x-text="doctor.name"
                                    class="sticky top-0 border p-2
                                            text-sm font-semibold text-gray-700
                                            min-w-40 max-w-40 z-10 bg-white ">
                                </th>
                            </template>
                        </tr>
                        </thead>
                        <tbody>
                        <template x-for="(slot, index) in slots"
                                  :key="`${slot}_${index}`">
                            <tr>
                                <td x-text="slot"
                                    class="sticky  border p-2
                                        text-sm font-semibold text-gray-700
                                         start-0 z-10 bg-white min-w-20 w-20">
                                </td>
                                <template x-for="(doctor, index2) in doctors"
                                          :key="`${doctor.id}_${slot}_${selectedMedicalCenterIndex}_${selectedDate}`"
                                >
                                    <template
                                            x-if="shouldShowCell(doctor.id, slot) && showCell"
                                            :key="`${doctor.id}_${slot}_${selectedMedicalCenterIndex}_${selectedDate}`">
                                        <td class="border p-2 min-w-40 max-w-40
                                    relative group"
                                            x-data="{ appointment: getAppointment(doctor.id, slot) }"
                                            :class="{
                                        'bg-gray-200': !getAppointment(doctor.id, slot) && !isTimeSlotInDoctorWorkingHours(slot, doctor)}"
                                            :rowspan="getAppointmentRowSpan(doctor.id, slot)"
                                        >
                                            <template
                                                    x-if="appointment"
                                                    :key="`${appointment.status}_${appointment.id}_${slot}`">
                                                <div
                                                        class="flex flex-col
                                                        items-center
                                                        justify-center
                                                        text-sm font-medium
                                                        h-full"
                                                >
                                                    <div
                                                            class="w-full p-1
                                                             rounded h-full"
                                                            :class="{
                                                         'bg-blue-100 text-blue-800': appointment.status === 'reserved',
                                                         'bg-yellow-100 text-yellow-800': appointment.status === 'waiting',
                                                         'bg-green-100 text-green-800': appointment.status === 'completed',
                                                         'bg-red-100 text-red-800': appointment.status === 'not_attended_without_telling',
                                                         'bg-orange-100 text-orange-800': appointment.status === 'not_attended_with_telling'
                                                     }">
                                                        <span class="block text-xs"
                                                              x-text="appointment.patient?.full_name"></span>
                                                        <span class="block text-xs"
                                                              x-text="'Duration: ' + appointment.duration + ' min'"></span>
                                                    </div>
                                                </div>
                                            </template>

                                            <div
                                                    class="absolute top-0 bottom-0 left-0 right-0 items-center justify-center hidden  group-hover:flex border-r-primary border-l-primary"
                                                    :class="{'bg-accent ':(
                                                    getAppointment(doctor.id,slot) || !isPastTimeSlot(slot))}">
                                                <template
                                                        x-if="appointment"
                                                        :key="`${appointment.status}_${appointment.id}`">
                                                    <x-mary-icon name="c-pencil"
                                                                 class="flex-1 w-8 h-8 cursor-pointer group-hover:text-warning"
                                                                 @click="showAddEditAppointmentModal=true; $wire.changeShowAddEditAppointmentModal(appointment, slot, doctor, selectedMedicalCenterObj)"/>
                                                </template>
                                                <template
                                                        x-if="appointment?.status==='reserved'"
                                                        :key="`${appointment.status}_${appointment.id}`">
                                                    <x-mary-icon
                                                            name="s-user"
                                                            class="flex-1 w-8 h-8 cursor-pointer group-hover:text-warning"
                                                            @click="$wire.setWaiting(appointment.id)"/>
                                                </template>

                                                <template
                                                        x-if="!appointment &&!isPastTimeSlot(slot)"
                                                        :key="`${!appointment}_${index}`">
                                                    <x-mary-icon name="c-plus"
                                                                 class="flex-1 w-8 h-8 cursor-pointer group-hover:text-primary"
                                                                 @click="showAddEditAppointmentModal=true; $wire.changeShowAddEditAppointmentModal(null, slot, doctor, selectedMedicalCenterObj)"/>
                                                </template>
                                            </div>
                                        </td>
                                    </template>

                                </template>
                            </tr>
                        </template>
                        </tbody>
                    </table>
                </template>
                <template
                        x-if=" !showCell"
                >

                    <div class="w-[calc(100%-2rem)] h-[calc(100%-2rem)]
                                            absolute
                                            top-0 left-0 right-0 bottom-0
                                        flex items-center justify-center
                                        bg-white">
                        Loading...
                    </div>
                </template>
                <template x-if=" doctors.length <= 0">
                    <div class="flex items-center justify-center w-full h-full">
                        <div class="text-center">
                            {{ __('no_appointments') }}
                        </div>
                    </div>
                </template>
            </div>
        </div>


    </div>

    <div class=" hidden lg:flex flex-col  rounded-xl  w-[296px]">
        @php
            $config = [
                'dateFormat' => 'm/d/Y',
                'inline' => true,
//                'defaultDate' => $selectedDate
            ];
        @endphp
        <x-mary-datepicker label=""
                           wire:model.live="selectedDate"
                           type="date"
                           icon="o-calendar"
                           :config="$config"
                           class="text-primary"/>
    </div>

    <x-mary-modal x-show="showAddEditAppointmentModal"
                  subtitle=""
                  box-class="border-2 border-primary rounded-0"
                  class="appointmentModal max-w-[90%]"
                  persistent>
        <x-mary-header
                title="{{ $selectedAppointment ? __('edit_appointment') : __('add_appointment') }}"
                subtitle=""
                size="text-2xl"
                class="mb-1"
                separator
                progress-indicator>
            @unless ($selectedAppointment)
                <x-slot:middle
                        class="!justify-center ">
                    <div
                            class="flex flex-col items-center justify-center md:flex-row">

                        <x-mary-dropdown
                                class="btn-outline !py-0.5 h-8
                                min-w-60 border-primary
                                hover:bg-inherit hover:text-gray-500
                                hover:border-primary text-gray-500
                                custom-dropdown-bg me-4">
                            <x-slot:label>
                                {{ $selectedPatient ? $selectedPatient->full_name : __('select patient') }}
                            </x-slot:label>
                            <x-mary-menu-item wire:click.stop=""
                                              class="bg-transparent">
                                <x-slot:title>
                                    <x-mary-input
                                            wire:keydown.enter.prevent="patients"
                                            wire:model.live.debounce.300ms="searchPatientWord"
                                            class="bg-inherit"
                                            type="text"/>
                                </x-slot:title>
                            </x-mary-menu-item>
                            @foreach ($this->patients() as $patient)
                                <x-mary-menu-item
                                        title="{{ $patient->full_name }}"
                                        wire:click=" selectPatient({{ $patient }}) "
                                />
                                <x-mary-menu-separator/>
                            @endforeach
                        </x-mary-dropdown>
                        <div
                                class="mx-4 divider divider-horizontal divider-primary">
                            {{ __('OR') }}</div>
                        @can('create', $patientClass)
                            <x-mary-button icon="o-plus"
                                           class="btn-primary "
                                           wire:click="createNewPatient"
                            >
                                {{ __('add_new_patient') }}
                            </x-mary-button>
                        @endcan
                    </div>
                </x-slot:middle>
            @endunless
            <x-slot:actions>
                <x-mary-button icon="o-x-mark"
                               x-on:click="showAddEditAppointmentModal=false;
                                $wire.hideAddEditAppointmentModal1()"
                               class=" btn-error btn-circle"/>
            </x-slot:actions>
        </x-mary-header>


        <div class="grid grid-cols-1 gap-2 lg:grid-cols-3 lg:gap-4">

            @if (($selectedAppointment || $selectedPatient) && !$isNewPatient)
                <div class="col-span-2">
                    <livewire:patient.view-patient :id="$selectedPatient->id"
                                                   :isNested="true"
                                                   :key="$selectedPatient->id.'_'.now()"/>

                </div>
            @elseif (!$selectedPatient && $isNewPatient && !$selectedAppointment)
                <div class="col-span-2">
                    <livewire:patient.create-patient :id="0"
                                                     :isNested="true"
                                                     :key="0"/>
                </div>
            @endif
            @if ($selectedAppointment && !$isNewPatient)
                <div class="col-span-1 p-4 bg-white rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold">
                        {{ __('appointment_info') }}</h3>
                    <div class="mt-2">
                        <livewire:appointment.view-appointment
                                :selectedPatient="$selectedPatient"
                                :selectedDoctor="$selectedDoctor"
                                :selectedMedicalCenter="$selectedMedicalCenterObj"
                                :selectedTreatment="$selectedDoctorTreatment"
                                :selectedAppointment="$selectedAppointment"
                                :key="$selectedPatient->id.'_'.now()"/>

                    </div>
                </div>
            @elseif ($selectedPatient && !$selectedAppointment && !$isNewPatient)
                <div class="col-span-1 p-4 bg-white rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold">
                        {{ __('appointment_info') }}</h3>
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
<script>

    document.addEventListener('alpine:init', () => {
        Alpine.data('appointment', () => ({
            init() {
                this.selectedDate = @json($selectedDate);
                this.selectedMedicalCenterIndex = 0;
                this.initAppointments();
            },
            showAddEditAppointmentModal: false,
            medicalCenters: [],
            selectedMedicalCenterIndex: 0,
            $selectedMedicalCenterObj: null,
            showAllDoctors: true,
            doctors: [],
            showCell: true,
            slots: [],
            selectedDate: @json($selectedDate),
            initAppointments() {
                this.medicalCenters = @json($medicalCenters);
                this.selectedMedicalCenterObj = this.medicalCenters[this.selectedMedicalCenterIndex];
                // this.selectedMedicalCenterObj = this.medicalCenters[0];
                this.selectMedicalCenterA(this.selectedMedicalCenterIndex);
                // this.selectMedicalCenterA(0);
                this.selectedDate = this.selectedDate ? this.selectedDate : Date.now();
            },
            selectMedicalCenterA(index = this.selectedMedicalCenterIndex) {
                this.selectedMedicalCenterIndex = index;
                const medicalCenter = this.medicalCenters[index];
                this.selectedMedicalCenterObj = medicalCenter;

                // Filter doctors based on showAllDoctors setting
                if (this.showAllDoctors) {
                    this.doctors = medicalCenter.doctors;
                } else {
                    this.doctors = medicalCenter.doctors.filter(doctor =>
                        doctor.doctor_schedule.length > 0 && doctor
                            .doctor_schedule.filter(s => s.day_of_week == new Date(this.selectedDate).getDay()).length > 0);
                    // this.doctors = medicalCenter.doctors.filter(doctor =>
                    //     doctor.doctor_schedule > 0
                    // );
                    console.log('medicalCenter.doctors', this.doctors);
                }

                const workingHours = medicalCenter.working_hours[0];
                this.slots = this.generateSlotsByStartTime(
                    workingHours.opening_time,
                    workingHours.closing_time
                );
            },
            afterDataChanged(event) {
                this.showCell = false;
                this._appointmentCache.clear();
                this.medicalCenters = JSON.parse(JSON.stringify(event
                    .detail[0]));
                // this.selectMedicalCenterA(this.selectedMedicalCenterIndex);
                // this.$nextTick(() => {
                //     this.initAppointments();
                // });
            },
            setSelectedDate(date) {
                setTimeout(() => {
                    this._appointmentCache.clear();

                    this.selectedDate = date.detail[0];
                    console.log('selectedDate', this.selectedDate);

                    this.selectMedicalCenterA();
                    this.showCell = true;
                }, 50)
            },

            isPastTimeSlot(timeSlot) {
                const [hours, minutes] = timeSlot.match(/(\d+):(\d+)/).slice(1);
                const period = timeSlot.match(/[AP]M/)[0];
                let hour24 = parseInt(hours);
                if (period === 'PM' && hour24 !== 12) hour24 += 12;
                if (period === 'AM' && hour24 === 12) hour24 = 0;

                const timeSlotDateTime = new Date(this.selectedDate);
                timeSlotDateTime.setHours(hour24, parseInt(minutes), 0, 0);

                const currentDateTime = new Date();
                return timeSlotDateTime < currentDateTime;
            }
            ,
            generateSlotsByStartTime(startTime, endTime) {

                const slots = [];

                let currentTime = new Date(`2000/01/01 ${startTime}`);
                const endTimeDate = new Date(`2000/01/01 ${endTime}`);

                while (currentTime <= endTimeDate) {
                    slots.push(currentTime.toLocaleTimeString('en-US', {
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: true
                    }));
                    currentTime = new Date(currentTime.getTime() + 15 * 60000); // Add 15 minutes
                }

                return slots;
            }
            ,

            isTimeSlotInDoctorWorkingHours(timeSlot, doctor) {
                const medicalCenter = this.medicalCenters[this.selectedMedicalCenterIndex];
                if (!medicalCenter) return false;

                const doctorSchedule = doctor.doctor_schedule.find(schedule =>
                    schedule.medical_center_id === medicalCenter.id
                );

                if (!doctorSchedule) return false;

                const [hours, minutes] = timeSlot.match(/(\d+):(\d+)/).slice(1);
                const period = timeSlot.match(/[AP]M/)[0];
                let hour24 = parseInt(hours);
                if (period === 'PM' && hour24 !== 12) hour24 += 12;
                if (period === 'AM' && hour24 === 12) hour24 = 0;

                const timeSlotDate = new Date(`2000/01/01 ${hour24}:${minutes}:00`);
                const startTime = new Date(`2000/01/01 ${doctorSchedule.start_time}`);
                const endTime = new Date(`2000/01/01 ${doctorSchedule.end_time}`);

                return timeSlotDate >= startTime && timeSlotDate <= endTime;
            }
            ,
            getAppointmentRowSpan(doctorId, timeSlot) {
                const appointment = this.getAppointment(doctorId, timeSlot);
                if (!appointment) return 1;
                return Math.max(1, Math.ceil(appointment.duration / 15));
            }
            ,

            // shouldShowCell(doctorId, timeSlot) {
            //     const appointment = this.getAppointment(doctorId, timeSlot);
            //     if (appointment) return true;
            //     const currentSlotIndex = this.slots.indexOf(timeSlot);
            //     if (!appointment) {
            //         console.log("i", this.getAppointmentRowSpan(doctorId, timeSlot));
            //         for (let i = 1; i <= this.getAppointmentRowSpan(doctorId, this.slots[currentSlotIndex - 1]); i++) {
            //             if (i > 1) {
            //                 return false;
            //             }
            //         }
            //     }
            //     return true;
            // },

            shouldShowCell(doctorId, timeSlot) {
                const appointment = this.getAppointment(doctorId, timeSlot);
                if (appointment) return true;

                const currentSlotIndex = this.slots.indexOf(timeSlot);

                // Check previous slots for appointments with rowspan > 1
                for (let i = currentSlotIndex - 1; i >= 0; i--) {
                    const previousSlot = this.slots[i];
                    const previousAppointment = this.getAppointment(doctorId, previousSlot);

                    if (previousAppointment) {
                        const rowSpan = Math.ceil(previousAppointment.duration / 15);
                        // If current slot falls within previous appointment's rowspan, hide it
                        if (currentSlotIndex - i < rowSpan) {
                            return false;
                        }
                    }
                }

                return true;
            }
            ,


            _appointmentCache: new Map(),

            getAppointment(doctorId, timeSlot) {
                // Create a unique cache key
                const cacheKey = `${doctorId}_${timeSlot}_${this.selectedMedicalCenterIndex}_${this.selectedDate}`;

                // Check if result is already cached
                if (this._appointmentCache.has(cacheKey)) {
                    return this._appointmentCache.get(cacheKey);
                }

                const medicalCenter = this.medicalCenters[this.selectedMedicalCenterIndex];
                if (!medicalCenter) return null;

                // Find the doctor's appointments in the current medical center
                const doctor = this.doctors.find(d => d.id === doctorId);
                if (!doctor) return null;

                // Convert timeSlot to a comparable format
                const [hours, minutes] = timeSlot.match(/(\d+):(\d+)/).slice(1);
                const period = timeSlot.match(/[AP]M/)[0];
                let hour24 = parseInt(hours);
                if (period === 'PM' && hour24 !== 12) hour24 += 12;
                if (period === 'AM' && hour24 === 12) hour24 = 0;

                // Find matching appointment
                const appointment = doctor.appointments?.find(app => {
                    const appTime = new Date(app.appointment_time);
                    const appHours = appTime.getHours();
                    const appMinutes = appTime.getMinutes();

                    return appHours === hour24 && appMinutes === parseInt(minutes) && app.medical_center_id === medicalCenter.id;
                });

                // Cache the result
                this._appointmentCache.set(cacheKey, appointment);

                return appointment;
            }
            ,

        }))
        ;

    });


    // Alpine.start();


</script>
