<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Auth\Access\Response;

class SettingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('settings', 'viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Setting $setting): bool
    {
        return $user->hasPermission('settings', 'view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('settings', 'create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Setting $setting): bool
    {
        return $user->hasPermission('settings', 'update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Setting $setting): bool
    {
        return $user->hasPermission('settings', 'delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Setting $setting): bool
    {
        return $user->hasPermission('settings', 'restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Setting $setting): bool
    {
        return $user->hasPermission('settings', 'forceDelete');
    }
}
