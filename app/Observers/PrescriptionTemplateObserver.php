<?php

namespace App\Observers;

use App\Models\PrescriptionTemplate;
use App\Traits\LogsTrait;

class PrescriptionTemplateObserver
{
    use LogsTrait;

    /**
     * Handle the PrescriptionTemplate "created" event.
     */
    public function created(PrescriptionTemplate $prescriptionTemplate): void
    {
        $this->makeLog('created', 'PrescriptionTemplate created', $prescriptionTemplate, $prescriptionTemplate->id);
    }

    /**
     * Handle the PrescriptionTemplate "updated" event.
     */
    public function updated(PrescriptionTemplate $prescriptionTemplate): void
    {
        $this->makeLog('updated', 'PrescriptionTemplate updated', $prescriptionTemplate, $prescriptionTemplate->id);
    }

    /**
     * Handle the PrescriptionTemplate "deleted" event.
     */
    public function deleted(PrescriptionTemplate $prescriptionTemplate): void
    {
        $this->makeLog('deleted', 'PrescriptionTemplate deleted', $prescriptionTemplate, $prescriptionTemplate->id);
    }

    /**
     * Handle the PrescriptionTemplate "restored" event.
     */
    public function restored(PrescriptionTemplate $prescriptionTemplate): void
    {
        $this->makeLog('restored', 'PrescriptionTemplate restored', $prescriptionTemplate, $prescriptionTemplate->id);
    }

    /**
     * Handle the PrescriptionTemplate "force deleted" event.
     */
    public function forceDeleted(PrescriptionTemplate $prescriptionTemplate): void
    {
        $this->makeLog('forceDeleted', 'PrescriptionTemplate force deleted', $prescriptionTemplate, $prescriptionTemplate->id);
    }
}
