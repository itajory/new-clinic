<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PatientRecord;
use Illuminate\Auth\Access\Response;

class PatientRecordPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('patient_records', 'viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PatientRecord $patientRecord): bool
    {
        return $user->hasPermission('patient_records', 'view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('patient_records', 'create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PatientRecord $patientRecord): bool
    {
        return $user->hasPermission('patient_records', 'update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PatientRecord $patientRecord): bool
    {
        return $user->hasPermission('patient_records', 'delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PatientRecord $patientRecord): bool
    {
        return $user->hasPermission('patient_records', 'restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PatientRecord $patientRecord): bool
    {
        return $user->hasPermission('patient_records', 'forceDelete');
    }
}
