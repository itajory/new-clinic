<?php

namespace App\Policies;

use App\Models\Treatment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TreatmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('treatments', 'viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Treatment $treatment): bool
    {
        return $user->hasPermission('treatments', 'view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('treatments', 'create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Treatment $treatment): bool
    {
        return $user->hasPermission('treatments', 'update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Treatment $treatment): bool
    {
        return $user->hasPermission('treatments', 'delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Treatment $treatment): bool
    {
        return $user->hasPermission('treatments', 'restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Treatment $treatment): bool
    {
        return $user->hasPermission('treatments', 'forceDelete');
    }
}