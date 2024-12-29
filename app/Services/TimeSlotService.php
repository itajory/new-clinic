<?php

namespace App\Services;

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

    public function generateRowForTimeSlot($time, $appointments, $rowIndex, &$appointmentSpans, $doctors, $selectedDate, $medicalCenter, $workingHoursService)
    {
        $timeDiff = Carbon::createFromFormat('g:i A', $time);
        $slotTime = $selectedDate->copy()
            ->addHours($timeDiff->hour)
            ->addMinutes($timeDiff->minute)
            ->addSeconds($timeDiff->second);

        $dayOfWeek = $selectedDate->dayOfWeek;
        $workingHoursCache = $workingHoursService->batchCheckWorkingHours($time, $doctors, $dayOfWeek, $medicalCenter);

        $row = [$time];
        foreach ($doctors as $columnIndex => $doctor) {
            $appointment = $this->getAppointmentForDoctor($doctor, $appointments, $slotTime);
            $workingHours = $workingHoursCache[$doctor->id] ?? [
                'isInMedicalCenterWorkingHours' => false,
                'isInDoctorWorkingHours' => false
            ];

            $rowData = $this->generateRowData($appointment, $workingHours);

            if ($appointment) {
                $this->handleAppointmentSpans($appointment, $rowData, $columnIndex, $rowIndex, $appointmentSpans);
            }

            $row[] = $rowData;
        }

        return $row;
    }

    private function getAppointmentForDoctor($doctor, $appointments, $slotTime)
    {
        return $appointments
            ->where('doctor_id', $doctor->id)
            ->first(function ($appointment) use ($slotTime) {
                $appointmentStart = Carbon::parse($appointment->appointment_time);
                $appointmentEnd = $appointmentStart->copy()->addMinutes($appointment->duration);
                return $slotTime->between($appointmentStart, $appointmentEnd);
            });
    }

    private function generateRowData($appointment, $workingHours)
    {
        return [
            'appointment' => $appointment,
            'isWorkingHours' => $workingHours['isInMedicalCenterWorkingHours'],
            'isDoctorWorkingHours' => $workingHours['isInDoctorWorkingHours'],
            'col_span' => 1,
            'row_span' => 1,
        ];
    }

    private function handleAppointmentSpans($appointment, &$rowData, $columnIndex, $rowIndex, &$appointmentSpans)
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