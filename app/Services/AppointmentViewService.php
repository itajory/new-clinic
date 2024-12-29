<?php

namespace App\Services;

class AppointmentViewService
{
    private static $statusClasses = [
        'reserved' => 'status-reserved',
        'waiting' => 'status-waiting',
        'completed' => 'status-completed',
        'not_attended_with_telling' => 'status-not-attended-telling',
        'not_attended_without_telling' => 'status-not-attended-no-telling'
    ];

    public static function getAppointmentClass($appointment): string
    {
        if (!$appointment['isWorkingHours']) {
            return 'bg-gray-300';
        }

        if ($appointment['appointment']) {
            return self::$statusClasses[$appointment['appointment']->status] ?? 'bg-red-500';
        }

        return (!$appointment['isDoctorWorkingHours'] ?? false) ? 'bg-gray-200' : 'bg-gray-50';
    }
}