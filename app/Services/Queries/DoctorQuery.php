<?php

namespace App\Services\Queries;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class DoctorQuery
{
    private const CACHE_DURATION = 300; // 5 minutes

    public function getDoctors($selectedMedicalCenter, $selectedTreatment, $dayOfWeek, $showAllDoctors)
    {
        $cacheKey = "doctors:{$selectedMedicalCenter}:{$selectedTreatment}:{$dayOfWeek}:{$showAllDoctors}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($selectedMedicalCenter, $selectedTreatment, $dayOfWeek, $showAllDoctors) {
            return $this->buildDoctorQuery(
                $selectedMedicalCenter,
                $selectedTreatment,
                $dayOfWeek,
                $showAllDoctors
            )->get();
        });
    }

    private function buildDoctorQuery($selectedMedicalCenter, $selectedTreatment, $dayOfWeek, $showAllDoctors)
    {
        $query = User::select('id', 'name', 'treatment_id', 'role_id')
            ->where('role_id', 2)
            ->with([
                'medicalCenters' => fn ($q) => $q->select('medical_centers.id')
                    ->where('id', $selectedMedicalCenter),
                'treatment:id,name',
                'doctorSchedule' => fn ($q) => $q->select(
                    'user_id', 'medical_center_id', 'day_of_week',
                    'start_time', 'end_time'
                )->where('medical_center_id', $selectedMedicalCenter),
            ]);

        if ($selectedMedicalCenter) {
            $query->whereHas('medicalCenters', fn ($q) => $q->where('id', $selectedMedicalCenter)
            );
        }

        if ($selectedTreatment) {
            $query->where('treatment_id', $selectedTreatment);
        }

        if (!$showAllDoctors) {
            $query->whereHas('doctorSchedule', function ($q) use ($selectedMedicalCenter, $dayOfWeek) {
                $q->where('medical_center_id', $selectedMedicalCenter)
                  ->where('day_of_week', $dayOfWeek);
            });
        }

        return $query;
    }
}
