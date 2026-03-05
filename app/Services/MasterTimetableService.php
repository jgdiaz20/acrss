<?php

namespace App\Services;

use App\Lesson;
use App\Room;
use App\SchoolClass;
use App\User;
use App\Subject;
use Carbon\Carbon;

class MasterTimetableService
{
    protected $timeService;
    protected $schedulingConflictService;

    public function __construct(TimeService $timeService, SchedulingConflictService $schedulingConflictService)
    {
        $this->timeService = $timeService;
        $this->schedulingConflictService = $schedulingConflictService;
    }

    /**
     * Generate master timetable data for a specific day
     */
    public function generateMasterTimetableData($weekday)
    {
        // Get all rooms
        $rooms = Room::orderBy('name')->get();
        
        // Generate time slots (7 AM to 9 PM with 30-minute intervals)
        $timeSlots = $this->timeService->generateTimeRange();
        
        // Get lessons for the specific weekday
        $lessons = Lesson::with(['class', 'teacher', 'room', 'subject'])
            ->where('weekday', $weekday)
            ->get()
            ->groupBy('room_id');

        // Create the master timetable matrix
        $timetableMatrix = [];
        
        foreach ($timeSlots as $timeSlot) {
            $row = [
                'time_slot' => $timeSlot,
                'rooms' => []
            ];
            
            foreach ($rooms as $room) {
                $roomLessons = $lessons->get($room->id, collect());
                
                // Find lesson that occupies this time slot
                $occupiedLesson = $roomLessons->first(function ($lesson) use ($timeSlot) {
                    $lessonStart = Carbon::createFromFormat('H:i:s', $lesson->getRawOriginal('start_time'));
                    $lessonEnd = Carbon::createFromFormat('H:i:s', $lesson->getRawOriginal('end_time'));
                    $slotStart = Carbon::createFromFormat('H:i', $timeSlot['start']);
                    $slotEnd = Carbon::createFromFormat('H:i', $timeSlot['end']);
                    
                    // Check if lesson occupies this time slot
                    return $lessonStart->lt($slotEnd) && $lessonEnd->gt($slotStart);
                });
                
                if ($occupiedLesson) {
                    $row['rooms'][] = [
                        'type' => 'lesson',
                        'lesson' => $occupiedLesson,
                        'display_data' => $this->formatLessonDisplay($occupiedLesson),
                        'css_class' => $this->getLessonCssClass($occupiedLesson)
                    ];
                } else {
                    $row['rooms'][] = [
                        'type' => 'empty',
                        'time_slot' => $timeSlot,
                        'room' => $room,
                        'css_class' => 'empty-slot available-for-scheduling'
                    ];
                }
            }
            
            $timetableMatrix[] = $row;
        }

        // Calculate available hours for each room
        $roomHoursAvailable = [];
        foreach ($rooms as $room) {
            $roomHoursAvailable[$room->id] = $this->calculateRoomAvailableHours($room->id, $weekday, $timeSlots);
        }

        return [
            'weekday' => $weekday,
            'weekday_name' => Lesson::WEEK_DAYS[$weekday] ?? 'Unknown',
            'rooms' => $rooms,
            'time_slots' => $timeSlots,
            'timetable_matrix' => $timetableMatrix,
            'room_hours_available' => $roomHoursAvailable,
            'statistics' => $this->generateStatistics($rooms, $lessons, $timeSlots)
        ];
    }

    /**
     * Get available time slots for scheduling in a specific room and day
     */
    public function getAvailableTimeSlots($roomId, $weekday, $duration = 60)
    {
        $room = Room::findOrFail($roomId);
        $timeSlots = $this->timeService->generateTimeRange();
        
        $lessons = Lesson::where('weekday', $weekday)
            ->where('room_id', $roomId)
            ->orderBy('start_time')
            ->get();

        $availableSlots = [];
        
        foreach ($timeSlots as $index => $timeSlot) {
            $slotStart = Carbon::createFromFormat('H:i', $timeSlot['start']);
            $slotEnd = Carbon::createFromFormat('H:i', $timeSlot['end']);
            $requiredEnd = $slotStart->copy()->addMinutes($duration);
            
            // Check if this slot is available
            $isAvailable = true;
            $conflictingLesson = null;
            
            foreach ($lessons as $lesson) {
                $lessonStart = Carbon::createFromFormat('H:i:s', $lesson->getRawOriginal('start_time'));
                $lessonEnd = Carbon::createFromFormat('H:i:s', $lesson->getRawOriginal('end_time'));
                
                // Check for overlap
                if ($slotStart->lt($lessonEnd) && $requiredEnd->gt($lessonStart)) {
                    $isAvailable = false;
                    $conflictingLesson = $lesson;
                    break;
                }
            }
            
            if ($isAvailable) {
                $availableSlots[] = [
                    'time_slot' => $timeSlot,
                    'start_time' => $slotStart->format('H:i'),
                    'end_time' => $requiredEnd->format('H:i'),
                    'start_formatted' => $slotStart->format('g:i A'),
                    'end_formatted' => $requiredEnd->format('g:i A'),
                    'duration_minutes' => $duration
                ];
            }
        }
        
        return $availableSlots;
    }

    /**
     * Get room utilization statistics
     */
    public function getRoomUtilizationStats($weekday = null)
    {
        $rooms = Room::all();
        $stats = [];
        
        foreach ($rooms as $room) {
            $query = Lesson::where('room_id', $room->id);
            
            if ($weekday) {
                $query->where('weekday', $weekday);
            }
            
            $totalLessons = $query->count();
            $totalMinutes = $query->get()->sum(function ($lesson) {
                return Carbon::createFromFormat('H:i:s', $lesson->getRawOriginal('start_time'))
                    ->diffInMinutes(Carbon::createFromFormat('H:i:s', $lesson->getRawOriginal('end_time')));
            });
            
            // Calculate utilization percentage (assuming 14-hour day = 840 minutes)
            $totalAvailableMinutes = 840; // 7 AM to 9 PM
            $utilizationPercentage = $totalAvailableMinutes > 0 ? ($totalMinutes / $totalAvailableMinutes) * 100 : 0;
            
            $stats[] = [
                'room' => $room,
                'total_lessons' => $totalLessons,
                'total_minutes' => $totalMinutes,
                'utilization_percentage' => round($utilizationPercentage, 2),
                'utilization_status' => $this->getUtilizationStatus($utilizationPercentage)
            ];
        }
        
        return $stats;
    }

    /**
     * Format lesson data for display in the master timetable
     */
    private function formatLessonDisplay($lesson)
    {
        return [
            'id' => $lesson->id,
            'subject_name' => $lesson->subject->name ?? 'No Subject',
            'subject_code' => $lesson->subject->code ?? '',
            'class_name' => $lesson->class->display_name ?? 'No Class',
            'teacher_name' => $lesson->teacher->name ?? 'No Teacher',
            'room_name' => $lesson->room->display_name ?? 'No Room',
            'start_time' => $lesson->start_time,
            'end_time' => $lesson->end_time,
            'duration' => $lesson->getDifferenceAttribute(),
            'is_lab_required' => $lesson->subject->requires_lab ?? false,
            'has_conflicts' => !empty($lesson->getConflicts($lesson->id))
        ];
    }

    /**
     * Get CSS class for lesson display based on various factors
     */
    private function getLessonCssClass($lesson)
    {
        $classes = ['lesson-slot'];
        
        // Add conflict indicator - exclude self from conflict check
        $conflicts = $lesson->getConflicts($lesson->id);
        if (!empty($conflicts)) {
            $classes[] = 'has-conflicts';
        }
        
        // Add lab indicator
        if ($lesson->subject && $lesson->subject->requires_lab) {
            $classes[] = 'lab-lesson';
        }
        
        // Add duration indicator
        $duration = $lesson->getDifferenceAttribute();
        if ($duration >= 120) { // 2+ hours
            $classes[] = 'long-lesson';
        } elseif ($duration <= 30) { // 30 minutes or less
            $classes[] = 'short-lesson';
        }
        
        return implode(' ', $classes);
    }

    /**
     * Generate statistics for the master timetable
     */
    private function generateStatistics($rooms, $lessons, $timeSlots)
    {
        $totalRooms = $rooms->count();
        $totalTimeSlots = count($timeSlots);
        $totalPossibleSlots = $totalRooms * $totalTimeSlots;
        
        // Count actual occupied slots more accurately
        $occupiedSlots = 0;
        foreach ($lessons as $roomLessons) {
            $occupiedSlots += $roomLessons->count();
        }
        
        $emptySlots = $totalPossibleSlots - $occupiedSlots;
        $utilizationPercentage = $totalPossibleSlots > 0 ? round(($occupiedSlots / $totalPossibleSlots) * 100, 2) : 0;
        
        return [
            'total_rooms' => $totalRooms,
            'total_time_slots' => $totalTimeSlots,
            'total_possible_slots' => $totalPossibleSlots,
            'occupied_slots' => $occupiedSlots,
            'empty_slots' => $emptySlots,
            'utilization_percentage' => round($utilizationPercentage, 2),
            'rooms_with_lessons' => $lessons->count(),
            'rooms_without_lessons' => $totalRooms - $lessons->count()
        ];
    }

    /**
     * Get utilization status based on percentage
     */
    private function getUtilizationStatus($percentage)
    {
        if ($percentage >= 80) {
            return 'high';
        } elseif ($percentage >= 50) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Get lessons that span multiple time slots
     */
    public function getMultiSlotLessons($weekday)
    {
        $lessons = Lesson::with(['class', 'teacher', 'room', 'subject'])
            ->where('weekday', $weekday)
            ->get();
        
        $multiSlotLessons = [];
        $timeSlots = $this->timeService->generateTimeRange();
        
        foreach ($lessons as $lesson) {
            $lessonStart = Carbon::createFromFormat('H:i:s', $lesson->getRawOriginal('start_time'));
            $lessonEnd = Carbon::createFromFormat('H:i:s', $lesson->getRawOriginal('end_time'));
            $lessonDuration = $lessonStart->diffInMinutes($lessonEnd);
            
            if ($lessonDuration > 30) { // More than one time slot
                $affectedSlots = [];
                
                foreach ($timeSlots as $index => $timeSlot) {
                    $slotStart = Carbon::createFromFormat('H:i', $timeSlot['start']);
                    $slotEnd = Carbon::createFromFormat('H:i', $timeSlot['end']);
                    
                    if ($lessonStart->lt($slotEnd) && $lessonEnd->gt($slotStart)) {
                        $affectedSlots[] = [
                            'index' => $index,
                            'time_slot' => $timeSlot,
                            'is_start' => $lessonStart->eq($slotStart),
                            'is_end' => $lessonEnd->eq($slotEnd)
                        ];
                    }
                }
                
                $multiSlotLessons[] = [
                    'lesson' => $lesson,
                    'duration_minutes' => $lessonDuration,
                    'affected_slots' => $affectedSlots,
                    'slot_count' => count($affectedSlots)
                ];
            }
        }
        
        return $multiSlotLessons;
    }

    /**
     * Calculate available hours for a specific room on a given day
     */
    private function calculateRoomAvailableHours($roomId, $weekday, $timeSlots)
    {
        $lessons = Lesson::where('weekday', $weekday)
            ->where('room_id', $roomId)
            ->get();

        $totalMinutesAvailable = 0;
        
        foreach ($timeSlots as $timeSlot) {
            $slotStart = Carbon::createFromFormat('H:i', $timeSlot['start']);
            $slotEnd = Carbon::createFromFormat('H:i', $timeSlot['end']);
            
            // Check if this slot is occupied
            $isOccupied = false;
            foreach ($lessons as $lesson) {
                $lessonStart = Carbon::createFromFormat('H:i:s', $lesson->getRawOriginal('start_time'));
                $lessonEnd = Carbon::createFromFormat('H:i:s', $lesson->getRawOriginal('end_time'));
                
                if ($lessonStart->lt($slotEnd) && $lessonEnd->gt($slotStart)) {
                    $isOccupied = true;
                    break;
                }
            }
            
            if (!$isOccupied) {
                $totalMinutesAvailable += 30; // Each slot is 30 minutes
            }
        }
        
        return round($totalMinutesAvailable / 60, 1); // Convert to hours with 1 decimal place
    }
}
