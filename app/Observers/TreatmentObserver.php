<?php

namespace App\Observers;

use App\Models\Treatment;
use App\Traits\LogsTrait;

class TreatmentObserver
{
    use LogsTrait;

    /**
     * Handle the Treatment "created" event.
     */
    public function created(Treatment $treatment): void
    {
        $this->makeLog('created', 'Treatment created', $treatment, $treatment->id);
    }

    /**
     * Handle the Treatment "updated" event.
     */
    public function updated(Treatment $treatment): void
    {
        $this->makeLog('updated', 'Treatment updated', $treatment, $treatment->id);
    }

    /**
     * Handle the Treatment "deleted" event.
     */
    public function deleted(Treatment $treatment): void
    {
        $this->makeLog('deleted', 'Treatment deleted', $treatment, $treatment->id);
    }

    /**
     * Handle the Treatment "restored" event.
     */
    public function restored(Treatment $treatment): void
    {
        $this->makeLog('restored', 'Treatment restored', $treatment, $treatment->id);
    }

    /**
     * Handle the Treatment "force deleted" event.
     */
    public function forceDeleted(Treatment $treatment): void
    {
        $this->makeLog('forceDeleted', 'Treatment force deleted', $treatment, $treatment->id);
    }
}
