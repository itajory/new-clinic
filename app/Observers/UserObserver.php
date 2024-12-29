<?php

namespace App\Observers;

use App\Models\User;
use App\Traits\LogsTrait;

class UserObserver
{
    use LogsTrait;

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->makeLog('created', 'User created', $user, $user->id);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $this->makeLog('updated', 'User updated', $user, $user->id);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $this->makeLog('deleted', 'User deleted', $user, $user->id);
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        $this->makeLog('restored', 'User restored', $user, $user->id);
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        $this->makeLog('forceDeleted', 'User force deleted', $user, $user->id);
    }
}
