<?php

namespace App\Services\Appointments;

use Carbon\Carbon;

class TimeSlotService
{
    public function generateSlotTimes()
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

    public function generateAppointmentGrid($timeSlots, $doctors, $appointments, $workingHoursCache, &$appointmentSpans)
    {
        $data = [['Time', ...$doctors]];
        $appointmentSpans = [];

        foreach ($timeSlots as $index => $time) {
            if ($time !== 'Time') {
                $row = $this->generateRowForTimeSlot(
                    $time,
                    $appointments,
                    $index,
                    $appointmentSpans,
                    $doctors,
                    $workingHoursCache
                );
                $data[] = $row;
            }
        }

        return $data;
    }

    private function generateRowForTimeSlot($time, $appointments, $rowIndex, &$appointmentSpans, $doctors, $workingHoursCache)
    {
        $row = [$time];

        foreach ($doctors as $columnIndex => $doctor) {
            $appointment = $this->findAppointmentForDoctor($doctor, $appointments, $time);
            $workingHours = $workingHoursCache[$time][$doctor->id] ?? [
                'isInMedicalCenterWorkingHours' => false,
                'isInDoctorWorkingHours' => false,
            ];

            $rowData = $this->generateRowData($appointment, $workingHours, $columnIndex, $rowIndex, $appointmentSpans);
            $row[] = $rowData;
        }

        return $row;
    }

    private function findAppointmentForDoctor($doctor, $appointments, $time)
    {
        $slotTime = Carbon::createFromFormat('g:i A', $time);

        return $appointments
            ->where('doctor_id', $doctor->id)
            ->first(function ($appointment) use ($slotTime) {
                $appointmentStart = Carbon::parse($appointment->appointment_time);
                $appointmentEnd = $appointmentStart->copy()->addMinutes($appointment->duration);

                return $slotTime->between($appointmentStart, $appointmentEnd);
            });
    }

    private function generateRowData($appointment, $workingHours, $columnIndex, $rowIndex, &$appointmentSpans)
    {
        $rowData = [
            'appointment' => $appointment,
            'isWorkingHours' => $workingHours['isInMedicalCenterWorkingHours'],
            'isDoctorWorkingHours' => $workingHours['isInDoctorWorkingHours'],
            'col_span' => 1,
            'row_span' => 1,
        ];

        if ($appointment) {
            $this->handleAppointmentSpan($appointment, $columnIndex, $rowIndex, $appointmentSpans, $rowData);
        }

        return $rowData;
    }

    private function handleAppointmentSpan($appointment, $columnIndex, $rowIndex, &$appointmentSpans, &$rowData)
    {
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
}
