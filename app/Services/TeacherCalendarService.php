<?php

namespace App\Services;

use App\Lesson;
use App\User;

class TeacherCalendarService
{
    public function generateTeacherCalendarData(User $teacher, $weekDays)
    {
        $lessons = Lesson::with(['class', 'room', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->get();

        $calendarData = [];
        
        foreach ($weekDays as $index => $day) {
            $dayLessons = $lessons->where('weekday', $index)->sortBy(function($lesson) {
                return \Carbon\Carbon::createFromFormat('H:i:s', $lesson->getRawOriginal('start_time'));
            });
            
            if ($dayLessons->count() > 0) {
                $calendarData[$index] = $dayLessons->map(function($lesson) {
                    return [
                        'class_name' => $lesson->class->name ?? 'Unknown Class',
                        'room_name' => $lesson->room->display_name ?? $lesson->room->name ?? 'No Room',
                        'subject_name' => $lesson->subject->name ?? 'No Subject',
                        'subject_code' => $lesson->subject->code ?? '',
                        'start_time' => $lesson->start_time,
                        'end_time' => $lesson->end_time,
                        'lesson_id' => $lesson->id
                    ];
                })->values();
            } else {
                $calendarData[$index] = [];
            }
        }

        return $calendarData;
    }

    public function generateStudentCalendarData(User $student, $weekDays)
    {
        $lessons = Lesson::with('class', 'room', 'teacher')
            ->where('class_id', $student->class_id)
            ->get();

        $calendarData = [];
        
        foreach ($weekDays as $index => $day) {
            $dayLessons = $lessons->where('weekday', $index)->sortBy(function($lesson) {
                return \Carbon\Carbon::createFromFormat('H:i:s', $lesson->getRawOriginal('start_time'));
            });
            
            if ($dayLessons->count() > 0) {
                $calendarData[$index] = $dayLessons->map(function($lesson) {
                    return [
                        'subject' => $lesson->class->name,
                        'room_name' => $lesson->room->display_name ?? $lesson->room->name ?? 'N/A',
                        'teacher_name' => $lesson->teacher->name ?? 'N/A',
                        'start_time' => $lesson->start_time,
                        'end_time' => $lesson->end_time,
                        'lesson_id' => $lesson->id
                    ];
                })->values();
            } else {
                $calendarData[$index] = [];
            }
        }

        return $calendarData;
    }
}
