<?php

namespace App\Http\Controllers;

use App\Lesson;
use App\Services\TeacherCalendarService;
use Illuminate\Http\Request;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $teacher = auth()->user();
        
        // Get this week's lessons
        $weekDays = \App\Lesson::WEEK_DAYS;
        $calendarData = (new TeacherCalendarService)->generateTeacherCalendarData($teacher, $weekDays);
        
        // Count total classes this week
        $totalClasses = 0;
        foreach($calendarData as $time => $days) {
            foreach($days as $value) {
                if (is_array($value)) {
                    $totalClasses++;
                }
            }
        }
        
        // Get today's classes
        // Convert Carbon's dayOfWeek (0=Sunday, 1=Monday) to our system (1=Monday, 2=Tuesday, etc.)
        $carbonDayOfWeek = now()->dayOfWeek;
        $today = $carbonDayOfWeek === 0 ? 7 : $carbonDayOfWeek; // Sunday becomes 7, others stay the same
        
        $todayClasses = Lesson::with('class', 'room')
            ->where('teacher_id', $teacher->id)
            ->where('weekday', $today)
            ->orderBy('start_time')
            ->get();
        
        // Get upcoming classes (next 3 days)
        $upcomingClasses = collect();
        for ($i = 1; $i <= 3; $i++) {
            $day = $today + $i;
            // Handle week rollover (if we go past Sunday=7, wrap to Monday=1)
            if ($day > 7) {
                $day = $day - 7;
            }
            
            $classes = Lesson::with('class', 'room')
                ->where('teacher_id', $teacher->id)
                ->where('weekday', $day)
                ->orderBy('start_time')
                ->get();
            
            if ($classes->count() > 0) {
                $upcomingClasses->put($weekDays[$day], $classes);
            }
        }

        return view('teacher.dashboard', compact('totalClasses', 'todayClasses', 'upcomingClasses', 'weekDays'));
    }
}
