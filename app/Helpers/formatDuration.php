<?php
if (!function_exists('formatDuration')) {
    function formatDuration(int $minutes): string
    {
        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . ($remainingMinutes > 0 ? $remainingMinutes . 'm' : '');
        }

        return $remainingMinutes . 'm';
    }
}
