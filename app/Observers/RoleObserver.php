<?php

namespace App\Observers;

use App\Models\Role;
use App\Traits\LogsTrait;

class RoleObserver
{
    use LogsTrait;

    /**
     * Handle the Role "created" event.
     */
    public function created(Role $role): void
    {
        $this->makeLog('created', 'Role created', $role, $role->id);
    }

    /**
     * Handle the Role "updated" event.
     */
    public function updated(Role $role): void
    {
        $this->makeLog('updated', 'Role updated', $role, $role->id);
    }

    /**
     * Handle the Role "deleted" event.
     */
    public function deleted(Role $role): void
    {
        $this->makeLog('deleted', 'Role deleted', $role, $role->id);
    }

    /**
     * Handle the Role "restored" event.
     */
    public function restored(Role $role): void
    {
        $this->makeLog('restored', 'Role restored', $role, $role->id);
    }

    /**
     * Handle the Role "force deleted" event.
     */
    public function forceDeleted(Role $role): void
    {
        $this->makeLog('forceDeleted', 'Role force deleted', $role, $role->id);
    }
}
