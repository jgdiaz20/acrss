<?php

namespace App\Rules;

use App\Lesson;
use Illuminate\Contracts\Validation\Rule;

class LessonTimeAvailabilityRule implements Rule
{
    private $lesson;
    private $conflictDetails = [];

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($lesson = null)
    {
        $this->lesson = $lesson;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            // Convert configured format to 24-hour format for database comparison
            $timeFormat = 'g:i A'; // Default format
            $startTime = \Carbon\Carbon::createFromFormat($timeFormat, $value)->format('H:i:s');
            $endTime = \Carbon\Carbon::createFromFormat($timeFormat, request()->input('end_time'))->format('H:i:s');
            
            $weekday = request()->input('weekday');
            $classId = request()->input('class_id');
            $teacherId = request()->input('teacher_id');
            $roomId = request()->input('room_id');
            
            // Check for conflicts and get details
            $conflicts = $this->getConflictDetails($weekday, $startTime, $endTime, $classId, $teacherId, $roomId);
            
            if (!empty($conflicts)) {
                $this->conflictDetails = $conflicts;
                return false;
            }
            
            return true;
        } catch (\Exception $e) {
            // If time format is invalid, let the date_format validation rule handle it
            return true;
        }
    }

    /**
     * Get detailed conflict information
     */
    private function getConflictDetails($weekday, $startTime, $endTime, $classId, $teacherId, $roomId)
    {
        $conflicts = [];
        
        $query = Lesson::with(['class', 'teacher', 'room', 'subject'])
            ->where('weekday', $weekday)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where([
                    ['start_time', '<', $endTime],
                    ['end_time', '>', $startTime],
                ]);
            });

        if ($this->lesson) {
            $query->where('id', '!=', $this->lesson);
        }

        $conflictingLessons = $query->get();

        foreach ($conflictingLessons as $lesson) {
            $conflictTypes = [];
            
            // Check class conflict
            if ($lesson->class_id == $classId) {
                $conflictTypes[] = "Class {$lesson->class->name}";
            }
            
            // Check teacher conflict
            if ($lesson->teacher_id == $teacherId) {
                $conflictTypes[] = "Teacher {$lesson->teacher->name}";
            }
            
            // Check room conflict
            if ($lesson->room_id == $roomId) {
                $conflictTypes[] = "Room {$lesson->room->display_name}";
            }
            
            if (!empty($conflictTypes)) {
                $conflicts[] = [
                    'types' => $conflictTypes,
                    'lesson' => $lesson,
                    'time' => "{$lesson->start_time} - {$lesson->end_time}"
                ];
            }
        }

        return $conflicts;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if (empty($this->conflictDetails)) {
            return 'This time is not available';
        }

        $messages = [];
        foreach ($this->conflictDetails as $conflict) {
            $types = implode(', ', $conflict['types']);
            $messages[] = "Conflict with {$types} at {$conflict['time']}";
        }

        return 'Scheduling conflict detected: ' . implode('; ', $messages) . '. Please choose a different time or resource.';
    }
}
