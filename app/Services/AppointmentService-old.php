<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\MedicalCenter;
use App\Models\Patient;
use App\Models\Treatment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AppointmentService
{
    public function getMedicalCenters()
    {
        return Cache::remember('medical_centers', 60 * 60, function () {
            return MedicalCenter::select('id', 'name')->get();
        });
    }

    public function getTreatments()
    {
        return Cache::remember('treatments', 60 * 60, function () {
            return Treatment::select('id', 'name')->get();
        });
    }

    public function getDoctors($selectedMedicalCenter, $selectedTreatment, $dayOfWeek, $showAllDoctors)
    {
        $cacheKey = "doctors:{$selectedMedicalCenter}:{$selectedTreatment}:{$dayOfWeek}:{$showAllDoctors}";

        return Cache::remember($cacheKey, 300, function () use ($selectedMedicalCenter, $selectedTreatment, $dayOfWeek, $showAllDoctors) {
            return User::select('id', 'name', 'treatment_id', 'role_id')
                ->where('role_id', 2)
                ->when($selectedMedicalCenter, fn($q) => 
                    $q->whereHas('medicalCenters', fn($sq) => $sq->where('id', $selectedMedicalCenter))
                )
                ->when($selectedTreatment, fn($q) => 
                    $q->where('treatment_id', $selectedTreatment)
                )
                ->when(!$showAllDoctors, function ($q) use ($selectedMedicalCenter, $dayOfWeek) {
                    $q->whereHas('doctorSchedule', function ($sq) use ($selectedMedicalCenter, $dayOfWeek) {
                        $sq->where('medical_center_id', $selectedMedicalCenter)
                           ->where('day_of_week', $dayOfWeek);
                    });
                })
                ->with([
                    'medicalCenters:id', 
                    'treatment:id,name', 
                    'doctorSchedule' => fn($q) => 
                        $q->where('medical_center_id', $selectedMedicalCenter)
                ])
                ->get();
        });
    }

    public function getAppointments($selectedMedicalCenter, $selectedDate)
    {
        $date = Carbon::createFromFormat('m/d/Y', $selectedDate)->startOfDay();

        return Appointment::select(
            'id', 'patient_id', 'doctor_id', 'treatment_id',
            'appointment_time', 'duration', 'status'
        )
            ->with([
                'patient:id,full_name',
                'doctor:id,name',
                'treatment:id,name'
            ])
            ->where('medical_center_id', $selectedMedicalCenter)
            ->whereRaw('DATE(appointment_time) = ?', [$date->toDateString()])
            ->get();
    }
    public function getOptimizedAppointments($selectedMedicalCenter, $selectedDate, $doctors)
    {
        $date = Carbon::createFromFormat('m/d/Y', $selectedDate)->startOfDay();

        $appointments = Appointment::select(
            'id', 'patient_id', 'doctor_id', 'treatment_id',
            'appointment_time', 'duration', 'status'
        )
            ->with([
                'patient:id,full_name',
                'doctor:id,name',
                'treatment:id,name'
            ])
            ->where('medical_center_id', $selectedMedicalCenter)
            ->whereRaw('DATE(appointment_time) = ?', [$date->toDateString()])
            ->get();

        // Optimize by preloading related data
        $doctorIds = $doctors->pluck('id');
        $appointments = $appointments->filter(function ($appointment) use ($doctorIds) {
            return $doctorIds->contains($appointment->doctor_id);
        });

        return $appointments;
    }
}
