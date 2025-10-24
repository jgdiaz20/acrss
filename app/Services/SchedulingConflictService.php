<?php

namespace App\Services;

use App\Lesson;
use App\User;
use App\Room;
use App\SchoolClass;

class SchedulingConflictService
{
    /**
     * Check for scheduling conflicts and return detailed information
     */
    public function checkConflicts($weekday, $startTime, $endTime, $classId, $teacherId, $roomId, $excludeLessonId = null)
    {
        $conflicts = [];
        
        // Convert time format if needed
        if (is_string($startTime)) {
            $startTime = \Carbon\Carbon::createFromFormat('H:i:s', $startTime)->format('H:i:s');
        }
        if (is_string($endTime)) {
            $endTime = \Carbon\Carbon::createFromFormat('H:i:s', $endTime)->format('H:i:s');
        }
        
        $query = Lesson::with(['class', 'teacher', 'room', 'subject'])
            ->where('weekday', $weekday)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where([
                    ['start_time', '<', $endTime],
                    ['end_time', '>', $startTime],
                ]);
            });

        if ($excludeLessonId) {
            $query->where('id', '!=', $excludeLessonId);
        }

        $conflictingLessons = $query->get();

        foreach ($conflictingLessons as $lesson) {
            $conflictTypes = [];
            $conflictDetails = [];
            
            // Check class conflict
            if ($lesson->class_id == $classId) {
                $conflictTypes[] = 'Class';
                $conflictDetails[] = [
                    'type' => 'Class',
                    'name' => $lesson->class->name ?? 'Unknown Class',
                    'id' => $lesson->class_id
                ];
            }
            
            // Check teacher conflict
            if ($lesson->teacher_id == $teacherId) {
                $conflictTypes[] = 'Teacher';
                $conflictDetails[] = [
                    'type' => 'Teacher',
                    'name' => $lesson->teacher->name ?? 'Unknown Teacher',
                    'id' => $lesson->teacher_id
                ];
            }
            
            // Check room conflict
            if ($lesson->room_id == $roomId) {
                $conflictTypes[] = 'Room';
                $conflictDetails[] = [
                    'type' => 'Room',
                    'name' => $lesson->room->display_name ?? $lesson->room->name ?? 'Unknown Room',
                    'id' => $lesson->room_id
                ];
            }
            
            if (!empty($conflictTypes)) {
                $conflicts[] = [
                    'lesson_id' => $lesson->id,
                    'conflict_types' => $conflictTypes,
                    'conflict_details' => $conflictDetails,
                    'time' => "{$lesson->start_time} - {$lesson->end_time}",
                    'subject' => $lesson->subject->name ?? 'Unknown Subject',
                    'existing_lesson' => $lesson
                ];
            }
        }

        return $conflicts;
    }

    /**
     * Validate teacher-subject assignment
     */
    public function validateTeacherSubjectAssignment($teacherId, $subjectId)
    {
        $teacher = User::find($teacherId);
        $subject = \App\Subject::find($subjectId);
        
        if (!$teacher) {
            return [
                'valid' => false,
                'message' => 'Selected teacher does not exist.'
            ];
        }
        
        if (!$subject) {
            return [
                'valid' => false,
                'message' => 'Selected subject does not exist.'
            ];
        }
        
        if (!$teacher->is_teacher) {
            return [
                'valid' => false,
                'message' => "User {$teacher->name} is not a teacher."
            ];
        }
        
        $teacherSubject = \App\TeacherSubject::where('teacher_id', $teacherId)
            ->where('subject_id', $subjectId)
            ->where('is_active', true)
            ->first();
        
        if (!$teacherSubject) {
            return [
                'valid' => false,
                'message' => "Teacher {$teacher->name} is not assigned to subject {$subject->name}. Please assign the teacher to this subject first."
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'Teacher-subject assignment is valid.'
        ];
    }

    /**
     * Validate room requirements for subject
     */
    public function validateRoomRequirements($roomId, $subjectId)
    {
        $room = Room::find($roomId);
        $subject = \App\Subject::find($subjectId);
        
        if (!$room) {
            return [
                'valid' => false,
                'message' => 'Selected room does not exist.'
            ];
        }
        
        if (!$subject) {
            return [
                'valid' => false,
                'message' => 'Selected subject does not exist.'
            ];
        }
        
        $issues = [];
        
        if ($subject->requires_lab && !$room->is_lab) {
            $issues[] = "Subject {$subject->name} requires a lab room, but {$room->display_name} is not a lab room.";
        }
        
        if ($subject->requires_equipment && !$room->has_equipment) {
            $issues[] = "Subject {$subject->name} requires a room with equipment, but {$room->display_name} does not have equipment.";
        }
        
        if (!empty($issues)) {
            return [
                'valid' => false,
                'message' => implode(' ', $issues)
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'Room requirements are satisfied.'
        ];
    }

    /**
     * Get comprehensive validation results for a lesson
     */
    public function validateLesson($weekday, $startTime, $endTime, $classId, $teacherId, $roomId, $subjectId, $excludeLessonId = null)
    {
        $results = [
            'valid' => true,
            'conflicts' => [],
            'issues' => [],
            'warnings' => []
        ];
        
        // Check time conflicts
        $conflicts = $this->checkConflicts($weekday, $startTime, $endTime, $classId, $teacherId, $roomId, $excludeLessonId);
        if (!empty($conflicts)) {
            $results['valid'] = false;
            $results['conflicts'] = $conflicts;
        }
        
        // Check teacher-subject assignment
        $teacherSubjectValidation = $this->validateTeacherSubjectAssignment($teacherId, $subjectId);
        if (!$teacherSubjectValidation['valid']) {
            $results['valid'] = false;
            $results['issues'][] = $teacherSubjectValidation['message'];
        }
        
        // Check room requirements
        $roomValidation = $this->validateRoomRequirements($roomId, $subjectId);
        if (!$roomValidation['valid']) {
            $results['valid'] = false;
            $results['issues'][] = $roomValidation['message'];
        }
        
        // Check if class is active
        $class = SchoolClass::find($classId);
        if ($class && !$class->is_active) {
            $results['warnings'][] = "Class {$class->name} is inactive.";
        }
        
        // Check if subject is active
        $subject = \App\Subject::find($subjectId);
        if ($subject && !$subject->is_active) {
            $results['warnings'][] = "Subject {$subject->name} is inactive.";
        }
        
        return $results;
    }
}
