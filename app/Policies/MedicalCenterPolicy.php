<?php

namespace App\Policies;

use App\Models\MedicalCenter;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MedicalCenterPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('medical_centers', 'viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MedicalCenter $medicalCenter): bool
    {
        return $user->hasPermission('medical_centers', 'view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('medical_centers', 'create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicalCenter $medicalCenter): bool
    {
        return $user->hasPermission('medical_centers', 'update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MedicalCenter $medicalCenter): bool
    {
        return $user->hasPermission('medical_centers', 'delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MedicalCenter $medicalCenter): bool
    {
        return $user->hasPermission('medical_centers', 'restore');

    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MedicalCenter $medicalCenter): bool
    {
        return $user->hasPermission('medical_centers', 'forceDelete');
    }
}
