<?php

namespace App\Http\Controllers;

use App\Services\TeacherCalendarService;
use Illuminate\Http\Request;

class StudentCalendarController extends Controller
{
    public function index()
    {
        $weekDays = \App\Lesson::WEEK_DAYS;
        // For students, we want to show their class schedule, not teacher schedule
        // We'll use the same service but filter by the student's class
        $calendarData = (new TeacherCalendarService)->generateStudentCalendarData(auth()->user(), $weekDays);

        return view('student.calendar', compact('weekDays', 'calendarData'));
    }
}
