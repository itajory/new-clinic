<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\MedicalCenter;
use App\Models\Treatment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AppointmentService
{
    private const CACHE_DURATION = 3600; // 1 hour
    private const DOCTORS_CACHE_DURATION = 300; // 5 minutes

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

    public function getDoctors($medicalCenterId, $treatmentId, $dayOfWeek, $showAllDoctors)
    {
        $cacheKey = "doctors:{$medicalCenterId}:{$treatmentId}:{$dayOfWeek}:{$showAllDoctors}";

        return Cache::remember($cacheKey, self::DOCTORS_CACHE_DURATION, function () use ($medicalCenterId, $treatmentId, $dayOfWeek, $showAllDoctors) {
            return User::select('id', 'name', 'treatment_id', 'role_id')
                ->where('role_id', 2)
                ->when($medicalCenterId, fn ($q) => $q->whereHas('medicalCenters', fn ($sq) => $sq->where('id', $medicalCenterId)))
                ->when($treatmentId, fn ($q) => $q->where('treatment_id', $treatmentId))
                ->when(!$showAllDoctors, fn ($q) => $q->whereHas('doctorSchedule', fn ($sq) => 
                    $sq->where('medical_center_id', $medicalCenterId)
                       ->where('day_of_week', $dayOfWeek)
                ))
                ->with([
                    'medicalCenters:id',
                    'treatment:id,name',
                    'doctorSchedule' => fn ($q) => $q->where('medical_center_id', $medicalCenterId),
                ])
                ->get();
        });
    }

    public function getAppointments($medicalCenterId, $date)
    {
        $dateCarbon = Carbon::createFromFormat('m/d/Y', $date);
        
        return Appointment::select(
            'id', 'patient_id', 'doctor_id', 'treatment_id',
            'appointment_time', 'duration', 'status'
        )
            ->with([
                'patient:id,full_name',
                'doctor:id,name',
                'treatment:id,name',
            ])
            ->where('medical_center_id', $medicalCenterId)
            ->whereRaw('DATE(appointment_time) = ?', [$dateCarbon->toDateString()])
            ->get();
    }

    public function getOptimizedAppointments($medicalCenterId, $date, $doctors)
    {
        $cacheKey = "appointments:{$medicalCenterId}:{$date}";
        
        return Cache::remember($cacheKey, 60, function () use ($medicalCenterId, $date) {
            return $this->getAppointments($medicalCenterId, $date);
        });
    }
}