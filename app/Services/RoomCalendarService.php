<?php

namespace App\Services;

use App\Lesson;
use App\Room;
use Carbon\Carbon;

class RoomCalendarService
{
    protected $timeService;

    public function __construct(TimeService $timeService)
    {
        $this->timeService = $timeService;
    }

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
                        'lesson_type' => $lesson->lesson_type,
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

    /**
     * Generate time-based timetable matrix for room timetable view
     * Similar to master timetable structure but for a single room across all days
     */
    public function generateRoomTimetableMatrix(Room $room, $weekDays)
    {
        // Generate time slots (7 AM to 9 PM with 30-minute intervals)
        $timeSlots = $this->timeService->generateTimeRange();
        
        // Get all lessons for this room
        $lessons = Lesson::with(['class', 'teacher', 'subject'])
            ->where('room_id', $room->id)
            ->get()
            ->groupBy('weekday');

        // Create the timetable matrix: [time_slot][day] = lesson or empty
        $timetableMatrix = [];
        
        foreach ($timeSlots as $timeSlot) {
            $row = [
                'time_slot' => $timeSlot,
                'days' => []
            ];
            
            // For each day of the week
            foreach ($weekDays as $dayNumber => $dayName) {
                $dayLessons = $lessons->get($dayNumber, collect());
                
                // Find lesson that occupies this time slot
                $occupiedLesson = $dayLessons->first(function ($lesson) use ($timeSlot) {
                    $lessonStart = Carbon::createFromFormat('H:i:s', $lesson->getRawOriginal('start_time'));
                    $lessonEnd = Carbon::createFromFormat('H:i:s', $lesson->getRawOriginal('end_time'));
                    $slotStart = Carbon::createFromFormat('H:i', $timeSlot['start']);
                    $slotEnd = Carbon::createFromFormat('H:i', $timeSlot['end']);
                    
                    // Check if lesson occupies this time slot
                    return $lessonStart->lt($slotEnd) && $lessonEnd->gt($slotStart);
                });
                
                if ($occupiedLesson) {
                    $row['days'][] = [
                        'type' => 'lesson',
                        'lesson' => $occupiedLesson,
                        'css_class' => $this->getLessonCssClass($occupiedLesson)
                    ];
                } else {
                    $row['days'][] = [
                        'type' => 'empty',
                        'css_class' => 'empty-slot available-for-scheduling'
                    ];
                }
            }
            
            $timetableMatrix[] = $row;
        }

        return [
            'time_slots' => $timeSlots,
            'timetable_matrix' => $timetableMatrix
        ];
    }

    /**
     * Get CSS class for lesson based on conflicts and duration
     */
    private function getLessonCssClass($lesson)
    {
        $classes = [];
        
        // Check for conflicts
        if (!empty($lesson->getConflicts($lesson->id))) {
            $classes[] = 'has-conflicts';
        }
        
        // Add duration-based classes
        $duration = $lesson->getDifferenceAttribute();
        if ($duration >= 180) { // 3 hours or more
            $classes[] = 'long-lesson';
        } elseif ($duration <= 60) { // 1 hour or less
            $classes[] = 'short-lesson';
        }
        
        return implode(' ', $classes);
    }
}
