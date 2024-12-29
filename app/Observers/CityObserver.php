<?php

namespace App\Observers;

use App\Models\City;
use App\Traits\LogsTrait;

class CityObserver
{
    use LogsTrait;

    /**
     * Handle the City "created" event.
     */
    public function created(City $city): void
    {
        $this->makeLog('created', 'City created', $city, $city->id);
    }

    /**
     * Handle the City "updated" event.
     */
    public function updated(City $city): void
    {
        // dd($city->getDirty());
        $this->makeLog('updated', 'City updated', $city, $city->id);
    }

    /**
     * Handle the City "deleted" event.
     */
    public function deleted(City $city): void
    {
        $this->makeLog('deleted', 'City deleted', $city, $city->id);
    }

    /**
     * Handle the City "restored" event.
     */
    public function restored(City $city): void
    {
        $this->makeLog('restored', 'City restored', $city, $city->id);
    }

    /**
     * Handle the City "force deleted" event.
     */
    public function forceDeleted(City $city): void
    {
        $this->makeLog('forceDeleted', 'City force deleted', $city, $city->id);
    }
}
