<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class WorkingHoursService
{
    private const CACHE_DURATION = 3600; // 1 hour

    public function batchCheckWorkingHours($time, $doctors, $dayOfWeek, $medicalCenter)
    {
        $cacheKey = "medical_center_hours:{$time}:{$medicalCenter->id}:{$dayOfWeek}";

        $medicalCenterHours = Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($time, $medicalCenter, $dayOfWeek) {
            return $this->isTimeSlotInMedicalCenterWorkingHours($time, $medicalCenter, $dayOfWeek);
        });

        return collect($doctors)->mapWithKeys(function ($doctor) use ($time, $medicalCenter, $dayOfWeek, $medicalCenterHours) {
            $doctorCacheKey = "doctor_hours:{$time}:{$doctor->id}:{$dayOfWeek}";

            $doctorHours = Cache::remember($doctorCacheKey, self::CACHE_DURATION, function () use ($time, $medicalCenter, $dayOfWeek, $doctor) {
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

    public function isTimeSlotInMedicalCenterWorkingHours($timeSlot, $medicalCenter, $dayOfWeek)
    {
        $workingHours = $medicalCenter->workingHours()
            ->where('day_of_week', $this->mapDayOfWeek($dayOfWeek))
            ->first();

        if (!$workingHours) {
            return false;
        }

        $timeSlot = Carbon::createFromFormat('g:i A', $timeSlot);
        $openTime = Carbon::parse($workingHours->opening_time);
        $closeTime = Carbon::parse($workingHours->closing_time)->subMinutes(15);

        return $timeSlot->between($openTime, $closeTime);
    }

    public function isTimeSlotInDoctorWorkingHours($timeSlot, $medicalCenter, $dayOfWeek, $doctor)
    {
        $timeSlot = Carbon::createFromFormat('g:i A', $timeSlot);
        $dayOfWeek = $this->mapDayOfWeek($dayOfWeek);

        $doctorSchedule = $doctor->doctorSchedule()
            ->where('medical_center_id', $medicalCenter->id)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$doctorSchedule) {
            return false;
        }

        $doctorStartTime = Carbon::parse($doctorSchedule->start_time);
        $doctorEndTime = Carbon::parse($doctorSchedule->end_time)->subMinutes(15);

        return $timeSlot->between($doctorStartTime, $doctorEndTime);
    }

    private function mapDayOfWeek($carbonDayOfWeek)
    {
        return $carbonDayOfWeek === 0 ? 7 : $carbonDayOfWeek;
    }
}