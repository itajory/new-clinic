<?php

namespace App\Observers;

use App\Models\MedicalCenter;
use App\Traits\LogsTrait;

class MedicalCenterObserver
{
    use LogsTrait;

    /**
     * Handle the MedicalCenter "created" event.
     */
    public function created(MedicalCenter $medicalCenter): void
    {
        $this->makeLog('created', 'MedicalCenter created', $medicalCenter, $medicalCenter->id);
    }

    /**
     * Handle the MedicalCenter "updated" event.
     */
    public function updated(MedicalCenter $medicalCenter): void
    {
        $this->makeLog('updated', 'MedicalCenter updated', $medicalCenter, $medicalCenter->id);
    }

    /**
     * Handle the MedicalCenter "deleted" event.
     */
    public function deleted(MedicalCenter $medicalCenter): void
    {
        $this->makeLog('deleted', 'MedicalCenter deleted', $medicalCenter, $medicalCenter->id);
    }

    /**
     * Handle the MedicalCenter "restored" event.
     */
    public function restored(MedicalCenter $medicalCenter): void
    {
        $this->makeLog('restored', 'MedicalCenter restored', $medicalCenter, $medicalCenter->id);
    }

    /**
     * Handle the MedicalCenter "force deleted" event.
     */
    public function forceDeleted(MedicalCenter $medicalCenter): void
    {
        $this->makeLog('forceDeleted', 'MedicalCenter force deleted', $medicalCenter, $medicalCenter->id);
    }
}
