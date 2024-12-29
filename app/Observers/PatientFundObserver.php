<?php

namespace App\Observers;

use App\Models\PatientFund;
use App\Traits\LogsTrait;

class PatientFundObserver
{
    use LogsTrait;

    /**
     * Handle the PatientFund "created" event.
     */
    public function created(PatientFund $patientFund): void
    {
        $this->makeLog('created', 'PatientFund created', $patientFund, $patientFund->id);
    }

    /**
     * Handle the PatientFund "updated" event.
     */
    public function updated(PatientFund $patientFund): void
    {
        $this->makeLog('updated', 'PatientFund updated', $patientFund, $patientFund->id);
    }

    /**
     * Handle the PatientFund "deleted" event.
     */
    public function deleted(PatientFund $patientFund): void
    {
        $this->makeLog('deleted', 'PatientFund deleted', $patientFund, $patientFund->id);
    }

    /**
     * Handle the PatientFund "restored" event.
     */
    public function restored(PatientFund $patientFund): void
    {
        $this->makeLog('restored', 'PatientFund restored', $patientFund, $patientFund->id);
    }

    /**
     * Handle the PatientFund "force deleted" event.
     */
    public function forceDeleted(PatientFund $patientFund): void
    {
        $this->makeLog('forceDeleted', 'PatientFund force deleted', $patientFund, $patientFund->id);
    }
}
