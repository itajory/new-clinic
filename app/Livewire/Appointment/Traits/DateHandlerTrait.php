<?php

namespace App\Livewire\Appointment\Traits;

use Carbon\Carbon;

trait DateHandlerTrait
{
    public function selectDate($date)
    {
        $this->loadDoctorsAndAppointments();
    }

    public function updatedSelectedDate($date)
    {
        $this->selectDate($date);
    }

    public function isFutureDateTime($time)
    {
        try {
            $timeDiff = Carbon::createFromFormat('g:i A', $time);
            $slotTime = Carbon::parse($this->selectedDate)
                ->setHour($timeDiff->hour)
                ->setMinute($timeDiff->minute)
                ->setSecond($timeDiff->second);

            return $slotTime->greaterThanOrEqualTo(Carbon::now());
        } catch (\Throwable $th) {
            return false;
        }
    }

    private function mapDayOfWeek($carbonDayOfWeek)
    {
        return $carbonDayOfWeek === 0 ? 7 : $carbonDayOfWeek;
    }
}