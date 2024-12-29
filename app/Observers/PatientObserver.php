<?php

namespace App\Observers;

use App\Models\Patient;
use App\Traits\LogsTrait;

class PatientObserver
{
    use LogsTrait;

    /**
     * Handle the Patient "created" event.
     */
    public function created(Patient $patient): void
    {
        $this->makeLog('created', 'Patient created', $patient, $patient->id);
    }

    /**
     * Handle the Patient "updated" event.
     */
    public function updated(Patient $patient): void
    {
        $this->makeLog('updated', 'Patient updated', $patient, $patient->id);
    }

    /**
     * Handle the Patient "deleted" event.
     */
    public function deleted(Patient $patient): void
    {
        $this->makeLog('deleted', 'Patient deleted', $patient, $patient->id);
    }

    /**
     * Handle the Patient "restored" event.
     */
    public function restored(Patient $patient): void
    {
        $this->makeLog('restored', 'Patient restored', $patient, $patient->id);
    }

    /**
     * Handle the Patient "force deleted" event.
     */
    public function forceDeleted(Patient $patient): void
    {
        $this->makeLog('forceDeleted', 'Patient force deleted', $patient, $patient->id);
    }
}
