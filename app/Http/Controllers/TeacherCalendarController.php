<?php

namespace App\Http\Controllers;

use App\Services\TeacherCalendarService;
use Illuminate\Http\Request;

class TeacherCalendarController extends Controller
{
    public function index()
    {
        $weekDays = \App\Lesson::WEEK_DAYS;
        $calendarData = (new TeacherCalendarService)->generateTeacherCalendarData(auth()->user(), $weekDays);

        return view('teacher.calendar', compact('weekDays', 'calendarData'));
    }
}
