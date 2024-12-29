<?php

namespace App\Policies;

use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DoctorSchedulePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('doctor_schedules', 'viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DoctorSchedule $doctorSchedule): bool
    {
        return $user->hasPermission('doctor_schedules', 'view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('doctor_schedules', 'create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DoctorSchedule $doctorSchedule): bool
    {
        return $user->hasPermission('doctor_schedules', 'update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DoctorSchedule $doctorSchedule): bool
    {
        return $user->hasPermission('doctor_schedules', 'delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DoctorSchedule $doctorSchedule): bool
    {
        return $user->hasPermission('doctor_schedules', 'restore');

    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DoctorSchedule $doctorSchedule): bool
    {
        return $user->hasPermission('doctor_schedules', 'forceDelete');
    }
}
