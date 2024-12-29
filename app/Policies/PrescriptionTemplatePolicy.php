<?php

namespace App\Policies;

use App\Models\PrescriptionTemplate;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PrescriptionTemplatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('prescription_templates', 'viewAny');
    }


    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PrescriptionTemplate $prescriptionTemplate): bool
    {
        return $user->hasPermission('prescription_templates', 'view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('prescription_templates', 'create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PrescriptionTemplate $prescriptionTemplate): bool
    {
        return $user->hasPermission('prescription_templates', 'update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PrescriptionTemplate $prescriptionTemplate): bool
    {
        return $user->hasPermission('prescription_templates', 'delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PrescriptionTemplate $prescriptionTemplate): bool
    {
        return $user->hasPermission('prescription_templates', 'restore');

    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PrescriptionTemplate $prescriptionTemplate): bool
    {
        return $user->hasPermission('prescription_templates', 'forceDelete');
    }
}
