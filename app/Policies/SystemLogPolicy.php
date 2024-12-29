<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SystemLog;
use Illuminate\Auth\Access\Response;

class SystemLogPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('system_logs', 'viewAny');
    }
}
