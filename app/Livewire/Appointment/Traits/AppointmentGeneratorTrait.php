<?php

namespace App\Livewire\Appointment\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

trait AppointmentGeneratorTrait
{
    public function generateAppointments()
    {
        $timeSlots = $this->generateSlotTimes();
        $appointments = $this->appointments;
        $data = [['Time', ...$this->doctors]];
        $appointmentSpans = [];

        foreach ($timeSlots as $index => $time) {
            if ($time !== 'Time') {
                $row = $this->generateRowForTimeSlot($time, $appointments, $index, $appointmentSpans);
                $data[] = $row;
            }
        }

        $this->generatedAppointments = $data;
        $this->appointmentSpans = $appointmentSpans;

        return $data;
    }

    private function generateSlotTimes()
    {
        $slots = ['Time'];
        $startQuarter = 28;
        $totalQuartersInDay = 96;

        for ($i = 0; $i < $totalQuartersInDay; ++$i) {
            $currentQuarter = ($startQuarter + $i) % $totalQuartersInDay;
            $totalMinutes = $currentQuarter * 15;
            $hours = (int) ($totalMinutes / 60);
            $minutes = $totalMinutes % 60;

            $slots[] = Carbon::createFromTime($hours, $minutes)->format('g:i A');
        }

        return $slots;
    }

    private function generateRowForTimeSlot($time, $appointments, $rowIndex, &$appointmentSpans)
    {
        $selectedDate = Carbon::createFromFormat(self::DATE_FORMAT, $this->selectedDate);
        $timeDiff = Carbon::createFromFormat('g:i A', $time);
        $slotTime = $selectedDate->copy()
            ->addHours($timeDiff->hour)
            ->addMinutes($timeDiff->minute)
            ->addSeconds($timeDiff->second);

        $dayOfWeek = $this->mapDayOfWeek($selectedDate->dayOfWeek);
        $workingHoursCache = $this->batchCheckWorkingHours($time, $this->doctors, $dayOfWeek);

        $row = [$time];
        foreach ($this->doctors as $columnIndex => $doctor) {
            $appointment = $this->getAppointmentsForDoctor($doctor, $appointments, $slotTime);
            $workingHours = $workingHoursCache[$doctor->id] ?? [
                'isInMedicalCenterWorkingHours' => false,
                'isInDoctorWorkingHours' => false,
            ];

            $rowData = [
                'appointment' => $appointment,
                'isWorkingHours' => $workingHours['isInMedicalCenterWorkingHours'],
                'isDoctorWorkingHours' => $workingHours['isInDoctorWorkingHours'],
                'col_span' => 1,
                'row_span' => 1,
            ];

            if ($appointment) {
                $appointmentDuration = $appointment->duration;
                $rowData['row_span'] = (int) ($appointmentDuration / 15) + ($appointmentDuration % 15 ? 1 : 0);

                if (!isset($appointmentSpans[$columnIndex]) || $appointmentSpans[$columnIndex]['appointment_id'] !== $appointment->id) {
                    $appointmentSpans[$columnIndex] = [
                        'appointment_id' => $appointment->id,
                        'start_row' => $rowIndex,
                        'span' => $rowData['row_span'],
                        'col_span' => 1,
                    ];
                } else {
                    ++$appointmentSpans[$columnIndex]['span'];
                }
            }

            $row[] = $rowData;
        }

        return $row;
    }

    private function getAppointmentsForDoctor($doctor, $appointments, $slotTime)
    {
        return $appointments
            ->where('doctor_id', $doctor->id)
            ->first(function ($appointment) use ($slotTime) {
                $appointmentStart = Carbon::parse($appointment->appointment_time);
                $appointmentEnd = $appointmentStart->copy()->addMinutes($appointment->duration);

                return $slotTime->between($appointmentStart, $appointmentEnd);
            });
    }

    private function batchCheckWorkingHours($time, $doctors, $dayOfWeek)
    {
        $medicalCenter = $this->selectedMedicalCenterObj;
        $cacheKey = "medical_center_hours:{$time}:{$medicalCenter->id}:{$dayOfWeek}";

        $medicalCenterHours = Cache::remember($cacheKey, 3600, function () use ($time, $medicalCenter, $dayOfWeek) {
            return $this->isTimeSlotInMedicalCenterWorkingHours($time, $medicalCenter, $dayOfWeek);
        });

        return collect($doctors)->mapWithKeys(function ($doctor) use ($time, $medicalCenter, $dayOfWeek, $medicalCenterHours) {
            $doctorCacheKey = "doctor_hours:{$time}:{$doctor->id}:{$dayOfWeek}";

            $doctorHours = Cache::remember($doctorCacheKey, 3600, function () use ($time, $medicalCenter, $dayOfWeek, $doctor) {
                return $this->isTimeSlotInDoctorWorkingHours($time, $medicalCenter, $dayOfWeek, $doctor);
            });

            return [
                $doctor->id => [
                    'isInMedicalCenterWorkingHours' => $medicalCenterHours,
                    'isInDoctorWorkingHours' => $doctorHours,
                ],
            ];
        })->all();
    }
}
