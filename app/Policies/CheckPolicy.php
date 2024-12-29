<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Check;
use Illuminate\Auth\Access\Response;

class CheckPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('checks', 'viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Check $check): bool
    {
        return $user->hasPermission('checks', 'view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('checks', 'create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Check $check): bool
    {
        return $user->hasPermission('checks', 'update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Check $check): bool
    {
        return $user->hasPermission('checks', 'delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Check $check): bool
    {
        return $user->hasPermission('checks', 'restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Check $check): bool
    {
        return $user->hasPermission('checks', 'forceDelete');
    }
}
