<?php

namespace App\Policies;

use App\Models\PatientFund;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PatientFundPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('patient_funds', 'viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PatientFund $patientFund): bool
    {
        return $user->hasPermission('patient_funds', 'view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('patient_funds', 'create');

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PatientFund $patientFund): bool
    {
        return $user->hasPermission('patient_funds', 'update');

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PatientFund $patientFund): bool
    {
        return $user->hasPermission('patient_funds', 'delete');

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PatientFund $patientFund): bool
    {
        return $user->hasPermission('patient_funds', 'restore');

    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PatientFund $patientFund): bool
    {
        return $user->hasPermission('patient_funds', 'forceDelete');

    }
}
