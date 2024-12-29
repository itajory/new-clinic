<?php

namespace App\Observers;

use App\Models\Bank;
use App\Traits\LogsTrait;

class BankObserver
{
    use LogsTrait;

    /**
     * Handle the Bank "created" event.
     */
    public function created(Bank $bank): void
    {
        $this->makeLog('created', 'Bank created', $bank, $bank->id);
    }

    /**
     * Handle the Bank "updated" event.
     */
    public function updated(Bank $bank): void
    {
        $this->makeLog('updated', 'Bank updated', $bank, $bank->id);
    }

    /**
     * Handle the Bank "deleted" event.
     */
    public function deleted(Bank $bank): void
    {
        $this->makeLog('deleted', 'Bank deleted', $bank, $bank->id);
    }

    /**
     * Handle the Bank "restored" event.
     */
    public function restored(Bank $bank): void
    {
        $this->makeLog('restored', 'Bank restored', $bank, $bank->id);
    }

    /**
     * Handle the Bank "force deleted" event.
     */
    public function forceDeleted(Bank $bank): void
    {
        $this->makeLog('forceDeleted', 'Bank force deleted', $bank, $bank->id);
    }
}
