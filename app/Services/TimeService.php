<?php

namespace App\Services;

use Carbon\Carbon;

class TimeService
{
    public function generateTimeRange($from = null, $to = null)
    {
        $from = $from ?: config('panel.school_start_hour', 7) . ':00';
        $to = $to ?: config('panel.school_end_hour', 21) . ':00';
        $interval = config('panel.time_slot_interval', 30);
        
        $time = Carbon::parse($from);
        $timeRange = [];
        $timeFormat = config('panel.lesson_time_format', 'g:i A');

        do 
        {
            $startTime = $time->format("H:i");
            $endTime = $time->addMinutes($interval)->format("H:i");
            
            array_push($timeRange, [
                'start' => $startTime,
                'end' => $endTime,
                'start_formatted' => Carbon::parse($startTime)->format($timeFormat),
                'end_formatted' => Carbon::parse($endTime)->format($timeFormat)
            ]);    
        } while ($time->format("H:i") !== $to);

        return $timeRange;
    }
}