<?php

namespace App\Services;

use App\Lesson;
use App\Room;

class RoomCalendarService
{
    public function generateRoomCalendarData(Room $room, $weekDays)
    {
        $lessons = Lesson::with(['class', 'teacher', 'subject'])
            ->where('room_id', $room->id)
            ->get();

        $calendarData = [];
        
        foreach ($weekDays as $index => $day) {
            $dayLessons = $lessons->where('weekday', $index)->sortBy(function($lesson) {
                return \Carbon\Carbon::createFromFormat('H:i:s', $lesson->getRawOriginal('start_time'));
            });
            
            if ($dayLessons->count() > 0) {
                $calendarData[$index] = $dayLessons->map(function($lesson) {
                    return [
                        'id' => $lesson->id,
                        'class_name' => $lesson->class->name ?? 'Unknown Class',
                        'teacher_name' => $lesson->teacher->name ?? 'No Teacher',
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
}
