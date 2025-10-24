<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lesson;
use App\Room;
use App\User;
use App\Subject;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ValidationController extends Controller
{
    /**
     * Check for scheduling conflicts
     */
    public function checkConflicts(Request $request): JsonResponse
    {
        $weekday = $request->input('weekday');
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        $classId = $request->input('class_id');
        $teacherId = $request->input('teacher_id');
        $roomId = $request->input('room_id');
        $excludeId = $request->input('exclude_id'); // For updates

        $conflicts = [];

        // Check for overlapping lessons
        $overlappingLessons = Lesson::where('weekday', $weekday)
            ->where(function($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                      ->orWhereBetween('end_time', [$startTime, $endTime])
                      ->orWhere(function($q) use ($startTime, $endTime) {
                          $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                      });
            });

        if ($excludeId) {
            $overlappingLessons->where('id', '!=', $excludeId);
        }

        $lessons = $overlappingLessons->with(['class', 'teacher', 'room', 'subject'])->get();
        
        // Debug: Check if we have any lessons and their teacher data
        \Log::info('Lessons found for conflict check:', [
            'lessons_count' => $lessons->count(),
            'lessons_data' => $lessons->map(function($lesson) {
                return [
                    'id' => $lesson->id,
                    'teacher_id' => $lesson->teacher_id,
                    'teacher_name' => $lesson->teacher ? $lesson->teacher->name : 'NO TEACHER RELATIONSHIP',
                    'teacher_loaded' => $lesson->relationLoaded('teacher')
                ];
            })->toArray()
        ]);

        foreach ($lessons as $lesson) {
            // Debug logging for each lesson
            \Log::info('Processing lesson for conflicts:', [
                'lesson_id' => $lesson->id,
                'teacher_id' => $lesson->teacher_id,
                'teacher_loaded' => $lesson->relationLoaded('teacher'),
                'teacher_exists' => $lesson->teacher ? 'yes' : 'no',
                'teacher_name' => $lesson->teacher ? $lesson->teacher->name : 'null'
            ]);
            
            // Check class conflict
            if ($lesson->class_id == $classId) {
                $className = $lesson->class->display_name;
                $conflicts[] = [
                    'type' => 'class',
                    'message' => "Class {$className} is already scheduled at this time - a class cannot have two lessons simultaneously",
                    'conflicting_lessons' => [[
                        'class' => $className,
                        'teacher' => $this->getTeacherName($lesson),
                        'subject' => $lesson->subject->name ?? 'Unknown Subject',
                        'room' => $lesson->room->display_name ?? 'Unknown Room',
                        'time' => $this->formatTimeRange($lesson->start_time, $lesson->end_time)
                    ]]
                ];
            }

            // Check teacher conflict
            if ($lesson->teacher_id == $teacherId) {
                $teacherName = $this->getTeacherName($lesson);
                $conflicts[] = [
                    'type' => 'teacher',
                    'message' => "Teacher {$teacherName} is already teaching at this time - this is a critical conflict that must be resolved",
                    'conflicting_lessons' => [[
                        'class' => $lesson->class->display_name,
                        'teacher' => $teacherName,
                        'subject' => $lesson->subject->name ?? 'Unknown Subject',
                        'room' => $lesson->room->display_name ?? 'Unknown Room',
                        'time' => $this->formatTimeRange($lesson->start_time, $lesson->end_time)
                    ]]
                ];
            }

            // Check room conflict
            if ($lesson->room_id == $roomId) {
                $roomName = $lesson->room->display_name ?? 'Unknown Room';
                $conflicts[] = [
                    'type' => 'room',
                    'message' => "Room {$roomName} is already occupied at this time - consider using a different room",
                    'conflicting_lessons' => [[
                        'class' => $lesson->class->display_name,
                        'teacher' => $this->getTeacherName($lesson),
                        'subject' => $lesson->subject->name ?? 'Unknown Subject',
                        'room' => $roomName,
                        'time' => $this->formatTimeRange($lesson->start_time, $lesson->end_time)
                    ]]
                ];
            }
        }

        // Debug logging
        \Log::info('Validation conflicts generated:', [
            'conflicts_count' => count($conflicts),
            'conflicts' => $conflicts
        ]);

        return response()->json([
            'conflicts' => $conflicts,
            'has_conflicts' => count($conflicts) > 0
        ]);
    }

    /**
     * Get available rooms for a time slot
     */
    public function getAvailableRooms(Request $request): JsonResponse
    {
        $weekday = $request->input('weekday');
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');

        // Get rooms that are occupied during the specified time
        $occupiedRoomIds = Lesson::where('weekday', $weekday)
            ->where(function($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                      ->orWhereBetween('end_time', [$startTime, $endTime])
                      ->orWhere(function($q) use ($startTime, $endTime) {
                          $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                      });
            })
            ->pluck('room_id');

        $availableRooms = Room::whereNotIn('id', $occupiedRoomIds)
            ->select('id', 'name', 'capacity')
            ->get()
            ->map(function($room) {
                return [
                    'id' => $room->id,
                    'name' => $room->name,
                    'capacity' => $room->capacity ?? 'Unknown'
                ];
            });

        return response()->json([
            'rooms' => $availableRooms
        ]);
    }

    /**
     * Get teachers assigned to a subject
     */
    public function getTeachersForSubject(Request $request, $subjectId): JsonResponse
    {
        $subject = Subject::findOrFail($subjectId);
        
        $teachers = $subject->teacherSubjects()
            ->where('is_active', true)
            ->with('teacher')
            ->get()
            ->map(function($teacherSubject) {
                return [
                    'id' => $teacherSubject->teacher->id,
                    'name' => $teacherSubject->teacher->name,
                    'email' => $teacherSubject->teacher->email
                ];
            });

        return response()->json([
            'teachers' => $teachers
        ]);
    }

    /**
     * Check if a teacher is available for a time slot
     */
    public function checkTeacherAvailability(Request $request): JsonResponse
    {
        $teacherId = $request->input('teacher_id');
        $weekday = $request->input('weekday');
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        $excludeId = $request->input('exclude_id');

        $conflicts = Lesson::where('teacher_id', $teacherId)
            ->where('weekday', $weekday)
            ->where(function($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                      ->orWhereBetween('end_time', [$startTime, $endTime])
                      ->orWhere(function($q) use ($startTime, $endTime) {
                          $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                      });
            });

        if ($excludeId) {
            $conflicts->where('id', '!=', $excludeId);
        }

        $conflictCount = $conflicts->count();

        return response()->json([
            'available' => $conflictCount === 0,
            'conflict_count' => $conflictCount
        ]);
    }

    /**
     * Get alternative time slots
     */
    public function getAlternativeTimeSlots(Request $request): JsonResponse
    {
        $weekday = $request->input('weekday');
        $classId = $request->input('class_id');
        $teacherId = $request->input('teacher_id');
        $roomId = $request->input('room_id');
        $duration = $request->input('duration', 60); // Duration in minutes

        $timeSlots = $this->generateTimeSlots();
        $availableSlots = [];

        foreach ($timeSlots as $slot) {
            $isAvailable = $this->isTimeSlotAvailable($slot, $weekday, $classId, $teacherId, $roomId);
            
            if ($isAvailable) {
                $availableSlots[] = [
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'display' => $slot['display'],
                    'confidence' => $this->calculateTimeConfidence($slot, $weekday, $classId, $teacherId, $roomId)
                ];
            }
        }

        // Sort by confidence (highest first)
        usort($availableSlots, function($a, $b) {
            return $b['confidence'] <=> $a['confidence'];
        });

        return response()->json([
            'time_slots' => array_slice($availableSlots, 0, 5) // Return top 5
        ]);
    }

    /**
     * Generate time slots for the day
     */
    private function generateTimeSlots(): array
    {
        $slots = [];
        $startHour = config('panel.school_start_hour', 7); // 7 AM (school hours start)
        $endHour = config('panel.school_end_hour', 21);    // 9 PM (school hours end)
        $interval = config('panel.time_slot_interval', 30); // 30 minutes

        for ($hour = $startHour; $hour < $endHour; $hour++) {
            for ($minute = 0; $minute < 60; $minute += $interval) {
                $startTime = sprintf('%02d:%02d:00', $hour, $minute);
                $endMinute = $minute + $interval;
                $endHourAdjusted = $endMinute >= 60 ? $hour + 1 : $hour;
                $endMinuteAdjusted = $endMinute >= 60 ? $endMinute - 60 : $endMinute;
                $endTime = sprintf('%02d:%02d:00', $endHourAdjusted, $endMinuteAdjusted);

                if ($endHourAdjusted <= $endHour) {
                    $slots[] = [
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'display' => $this->formatTimeRange($startTime, $endTime)
                    ];
                }
            }
        }

        return $slots;
    }

    /**
     * Check if a time slot is available
     */
    private function isTimeSlotAvailable(array $slot, int $weekday, int $classId, int $teacherId, int $roomId): bool
    {
        $conflicts = Lesson::where('weekday', $weekday)
            ->where(function($query) use ($slot) {
                $query->whereBetween('start_time', [$slot['start_time'], $slot['end_time']])
                      ->orWhereBetween('end_time', [$slot['start_time'], $slot['end_time']])
                      ->orWhere(function($q) use ($slot) {
                          $q->where('start_time', '<=', $slot['start_time'])
                            ->where('end_time', '>=', $slot['end_time']);
                      });
            })
            ->where(function($query) use ($classId, $teacherId, $roomId) {
                $query->where('class_id', $classId)
                      ->orWhere('teacher_id', $teacherId)
                      ->orWhere('room_id', $roomId);
            })
            ->count();

        return $conflicts === 0;
    }

    /**
     * Calculate confidence for a time slot
     */
    private function calculateTimeConfidence(array $slot, int $weekday, int $classId, int $teacherId, int $roomId): float
    {
        // Base confidence
        $confidence = 0.5;

        // Check if all resources are available
        $classConflicts = Lesson::where('weekday', $weekday)
            ->where('class_id', $classId)
            ->where(function($query) use ($slot) {
                $query->whereBetween('start_time', [$slot['start_time'], $slot['end_time']])
                      ->orWhereBetween('end_time', [$slot['start_time'], $slot['end_time']]);
            })
            ->count();

        $teacherConflicts = Lesson::where('weekday', $weekday)
            ->where('teacher_id', $teacherId)
            ->where(function($query) use ($slot) {
                $query->whereBetween('start_time', [$slot['start_time'], $slot['end_time']])
                      ->orWhereBetween('end_time', [$slot['start_time'], $slot['end_time']]);
            })
            ->count();

        $roomConflicts = Lesson::where('weekday', $weekday)
            ->where('room_id', $roomId)
            ->where(function($query) use ($slot) {
                $query->whereBetween('start_time', [$slot['start_time'], $slot['end_time']])
                      ->orWhereBetween('end_time', [$slot['start_time'], $slot['end_time']]);
            })
            ->count();

        // Increase confidence if no conflicts
        if ($classConflicts === 0) $confidence += 0.2;
        if ($teacherConflicts === 0) $confidence += 0.2;
        if ($roomConflicts === 0) $confidence += 0.1;

        return min($confidence, 1.0);
    }

    /**
     * Get teacher name with fallback
     */
    private function getTeacherName($lesson)
    {
        // Try to get from relationship first
        if ($lesson->teacher && $lesson->teacher->name) {
            return $lesson->teacher->name;
        }
        
        // Fallback: get directly from database
        if ($lesson->teacher_id) {
            $teacher = User::find($lesson->teacher_id);
            if ($teacher && $teacher->name) {
                return $teacher->name;
            }
        }
        
        return 'Unknown Teacher';
    }

    /**
     * Format time range for display
     */
    private function formatTimeRange(string $startTime, string $endTime): string
    {
        $start = \Carbon\Carbon::createFromFormat('H:i:s', $startTime);
        $end = \Carbon\Carbon::createFromFormat('H:i:s', $endTime);
        
        return $start->format('g:i A') . ' - ' . $end->format('g:i A');
    }
}
