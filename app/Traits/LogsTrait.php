<?php

namespace App\Traits;

use App\Models\SystemLog;

trait LogsTrait
{
    public function makeLog($eventType, $eventDescription, $table, $tableId = null)
    {
        $user = auth()->user();
        if ($user) {
            SystemLog::create([
                'user_id' => $user->id,
                'medical_center_id' => $user->medical_center_id,
                'details' => $table,
                'table_id' => $tableId,
                'event_type' => $eventType,
                'event_description' => $eventDescription,
            ]);
        }
    }
}
