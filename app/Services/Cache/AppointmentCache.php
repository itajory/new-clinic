<?php

namespace App\Services\Cache;

use App\Models\Appointment;
use App\Models\MedicalCenter;
use App\Models\Treatment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AppointmentCache
{
    private const CACHE_DURATION = 3600; // 1 hour
    private const SHORT_CACHE_DURATION = 300; // 5 minutes

    public function getMedicalCenters()
    {
        return Cache::remember('medical_centers', self::CACHE_DURATION, function () {
            return MedicalCenter::select('id', 'name')->get();
        });
    }

    public function getTreatments()
    {
        return Cache::remember('treatments', self::CACHE_DURATION, function () {
            return Treatment::select('id', 'name')->get();
        });
    }

    public function getAppointments($selectedMedicalCenter, $selectedDate)
    {
        $date = Carbon::createFromFormat('m/d/Y', $selectedDate)->startOfDay();
        $cacheKey = "appointments:{$selectedMedicalCenter}:{$date->toDateString()}";

        return Cache::remember($cacheKey, self::SHORT_CACHE_DURATION, function () use ($selectedMedicalCenter, $date) {
            return Appointment::select(
                'id', 'patient_id', 'doctor_id', 'treatment_id',
                'appointment_time', 'duration', 'status'
            )
            ->with([
                'patient:id,full_name',
                'doctor:id,name',
                'treatment:id,name',
            ])
            ->where('medical_center_id', $selectedMedicalCenter)
            ->whereDate('appointment_time', $date)
            ->get();
        });
    }

    public function getOptimizedAppointments($selectedMedicalCenter, $selectedDate, $doctors)
    {
        $date = Carbon::createFromFormat('m/d/Y', $selectedDate)->startOfDay();
        $doctorIds = $doctors->pluck('id');
        $cacheKey = "optimized_appointments:{$selectedMedicalCenter}:{$date->toDateString()}:{$doctorIds->implode(',')}";

        return Cache::remember($cacheKey, self::SHORT_CACHE_DURATION, function () use ($selectedMedicalCenter, $date, $doctorIds) {
            return Appointment::select(
                'id', 'patient_id', 'doctor_id', 'treatment_id',
                'appointment_time', 'duration', 'status'
            )
            ->with([
                'patient:id,full_name',
                'doctor:id,name',
                'treatment:id,name',
            ])
            ->where('medical_center_id', $selectedMedicalCenter)
            ->whereDate('appointment_time', $date)
            ->whereIn('doctor_id', $doctorIds)
            ->get();
        });
    }
}
