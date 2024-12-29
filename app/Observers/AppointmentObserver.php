<?php

namespace App\Observers;

use App\Traits\LogsTrait;
use App\Models\Appointment;
use App\Events\AppointmentUpdated;

class AppointmentObserver
{
    use LogsTrait;

    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        $this->makeLog('created', 'Appointment created', $appointment, $appointment->id);
//        event(new AppointmentUpdated());
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        $this->makeLog('updated', 'Appointment updated', $appointment, $appointment->id);
//        event(new AppointmentUpdated());
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        $this->makeLog('deleted', 'Appointment deleted', $appointment, $appointment->id);
//        event(new AppointmentUpdated());
    }

    /**
     * Handle the Appointment "restored" event.
     */
    public function restored(Appointment $appointment): void
    {
        $this->makeLog('restored', 'Appointment restored', $appointment, $appointment->id);
//        event(new AppointmentUpdated());
    }

    /**
     * Handle the Appointment "force deleted" event.
     */
    public function forceDeleted(Appointment $appointment): void
    {
        $this->makeLog('forceDeleted', 'Appointment force deleted', $appointment, $appointment->id);
//        event(new AppointmentUpdated());
    }
}
