<?php

namespace App\Services\Appointments;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class WorkingHoursService
{
    private const CACHE_DURATION = 300; // 5 minutes

    public function batchCheckWorkingHours($timeSlots, $doctors, $dayOfWeek, $medicalCenterId, $medicalCenter)
    {
        $cacheKey = "working_hours_batch:{$medicalCenterId}:{$dayOfWeek}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($timeSlots, $doctors, $dayOfWeek, $medicalCenter) {
            $result = [];

            foreach ($timeSlots as $time) {
                if ($time === 'Time') {
                    continue;
                }

                $medicalCenterHours = $this->isTimeSlotInMedicalCenterWorkingHours(
                    $time,
                    $medicalCenter,
                    $dayOfWeek
                );

                foreach ($doctors as $doctor) {
                    $result[$time][$doctor->id] = [
                        'isInMedicalCenterWorkingHours' => $medicalCenterHours,
                        'isInDoctorWorkingHours' => $this->isTimeSlotInDoctorWorkingHours(
                            $time,
                            $medicalCenter,
                            $dayOfWeek,
                            $doctor
                        ),
                    ];
                }
            }

            return $result;
        });
    }

    private function isTimeSlotInMedicalCenterWorkingHours($timeSlot, $medicalCenter, $dayOfWeek)
    {
        $workingHours = $medicalCenter->workingHours()
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$workingHours) {
            return false;
        }

        $timeSlot = Carbon::createFromFormat('g:i A', $timeSlot);
        $openTime = Carbon::parse($workingHours->opening_time);
        $closeTime = Carbon::parse($workingHours->closing_time)->subMinutes(15);

        return $timeSlot->between($openTime, $closeTime);
    }

    private function isTimeSlotInDoctorWorkingHours($timeSlot, $medicalCenter, $dayOfWeek, $doctor)
    {
        $doctorSchedule = $doctor->doctorSchedule()
            ->where('medical_center_id', $medicalCenter->id)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$doctorSchedule) {
            return false;
        }

        $timeSlot = Carbon::createFromFormat('g:i A', $timeSlot);
        $doctorStartTime = Carbon::parse($doctorSchedule->start_time);
        $doctorEndTime = Carbon::parse($doctorSchedule->end_time)->subMinutes(15);

        return $timeSlot->between($doctorStartTime, $doctorEndTime);
    }
}
