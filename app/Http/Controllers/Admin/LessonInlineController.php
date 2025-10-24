<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lesson;
use App\Subject;
use App\User;
use App\Room;
use App\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class LessonInlineController extends Controller
{
    /**
     * Get form data for lesson creation/editing
     */
    public function getFormData(): JsonResponse
    {
        abort_if(Gate::denies('lesson_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        try {
            $data = [
                'classes' => SchoolClass::active()
                    ->with(['program', 'gradeLevel'])
                    ->get()
                    ->map(function ($class) {
                        return [
                            'id' => $class->id,
                            'name' => $class->display_name,
                            'program' => $class->program->name ?? 'N/A',
                            'program_type' => $class->program->type ?? null,
                            'grade_level' => $class->gradeLevel->level_name ?? 'N/A'
                        ];
                    }),
                
                'teachers' => User::where('is_teacher', true)  // Add is_teacher flag check
                    ->whereHas('roles', function($query) {
                        $query->where('title', 'Teacher'); // Teacher role
                    })->with('subjects')
                ->get()
                ->map(function ($teacher) {
                    return [
                        'id' => $teacher->id,
                        'name' => $teacher->name,
                        'subjects' => $teacher->subjects->pluck('name')->toArray()
                    ];
                }),
                
                'rooms' => Room::all()
                    ->map(function ($room) {
                        return [
                            'id' => $room->id,
                            'name' => $room->name,
                            'capacity' => $room->capacity,
                            'type' => $room->type,
                            'equipment' => $room->equipment
                        ];
                    }),
                
                'subjects' => Subject::active()
                    ->get()
                    ->map(function ($subject) {
                        return [
                            'id' => $subject->id,
                            'name' => $subject->name,
                            'code' => $subject->code,
                            'type' => $subject->type,
                            'credits' => $subject->credits,
                            'requires_lab' => $subject->requires_lab,
                            'requires_equipment' => $subject->requires_equipment
                        ];
                    }),
                
                'weekdays' => Lesson::WEEK_DAYS
            ];
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch form data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get lesson details for editing
     */
    public function getLesson($id): JsonResponse
    {
        abort_if(Gate::denies('lesson_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        try {
            $lesson = Lesson::with(['class', 'teacher', 'room', 'subject'])
                ->findOrFail($id);
            
            $data = [
                'id' => $lesson->id,
                'class_id' => $lesson->class_id,
                'class_name' => $lesson->class->display_name ?? 'N/A',
                'teacher_id' => $lesson->teacher_id,
                'teacher_name' => $lesson->teacher->name ?? 'N/A',
                'room_id' => $lesson->room_id,
                'room_name' => $lesson->room->name ?? 'N/A',
                'subject_id' => $lesson->subject_id,
                'subject_name' => $lesson->subject->name ?? 'N/A',
                'weekday' => $lesson->weekday,
                'weekday_name' => Lesson::WEEK_DAYS[$lesson->weekday] ?? 'N/A',
                'start_time' => $lesson->start_time,
                'end_time' => $lesson->end_time
            ];
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch lesson',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Create a new lesson
     */
    public function store(Request $request): JsonResponse
    {
        abort_if(Gate::denies('lesson_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        try {
            $validator = $this->validateLesson($request);
            
            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'messages' => $validator->errors()
                ], 422);
            }
            
            // Check for conflicts
            $conflicts = $this->checkConflictsInternal($request->all());
            if (!empty($conflicts)) {
                return response()->json([
                    'error' => 'Conflicts detected',
                    'conflicts' => $conflicts
                ], 409);
            }
            
            $lesson = Lesson::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Lesson created successfully',
                'lesson' => $lesson->load('class', 'teacher', 'room', 'subject')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create lesson',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update an existing lesson
     */
    public function update(Request $request, $id): JsonResponse
    {
        abort_if(Gate::denies('lesson_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        try {
            $lesson = Lesson::findOrFail($id);
            
            $validator = $this->validateLesson($request);
            
            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'messages' => $validator->errors()
                ], 422);
            }
            
            // Check for conflicts (excluding current lesson)
            $conflicts = $this->checkConflictsInternal($request->all(), $id);
            if (!empty($conflicts)) {
                return response()->json([
                    'error' => 'Conflicts detected',
                    'conflicts' => $conflicts
                ], 409);
            }
            
            $lesson->update($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Lesson updated successfully',
                'lesson' => $lesson->load('class', 'teacher', 'room', 'subject')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update lesson',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete a lesson
     */
    public function destroy($id): JsonResponse
    {
        abort_if(Gate::denies('lesson_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        try {
            $lesson = Lesson::with('class', 'teacher', 'room', 'subject')->findOrFail($id);
            
            // Store lesson details for response
            $lessonDetails = [
                'day' => \App\Lesson::WEEK_DAYS[$lesson->weekday] ?? 'Unknown',
                'time' => $lesson->start_time . ' - ' . $lesson->end_time,
                'class' => $lesson->class->display_name ?? 'Unknown',
                'teacher' => $lesson->teacher->name ?? 'Unknown',
                'subject' => $lesson->subject->name ?? 'Unknown'
            ];
            
            $lesson->forceDelete();
            
            return response()->json([
                'success' => true,
                'message' => 'Lesson deleted successfully',
                'lesson_details' => $lessonDetails
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete lesson',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Check for scheduling conflicts
     */
    public function checkConflicts(Request $request): JsonResponse
    {
        abort_if(Gate::denies('lesson_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        try {
            $data = $request->all();
            $conflicts = $this->checkConflictsInternal($data, $data['id'] ?? null);
            
            return response()->json([
                'conflicts' => $conflicts
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to check conflicts',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Validate lesson data
     */
    private function validateLesson(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:school_classes,id',
            'teacher_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'weekday' => [
                'required',
                'integer',
                'between:1,7',
                function ($attribute, $value, $fail) use ($request) {
                    // Weekend validation: Only diploma programs can have Saturday/Sunday classes
                    if (in_array($value, [6, 7])) {
                        $class = SchoolClass::find($request->input('class_id'));
                        if ($class && $class->program) {
                            if ($class->program->type !== 'diploma') {
                                $programTypeName = $class->program->type === 'senior_high' 
                                    ? 'Senior High School' 
                                    : ucfirst(str_replace('_', ' ', $class->program->type));
                                $fail('Weekend classes (Saturday/Sunday) are only available for Diploma Programs. This class belongs to ' . $programTypeName . ' program.');
                            }
                        }
                    }
                }
            ],
            'start_time' => 'required|date_format:g:i A',
            'end_time' => 'required|date_format:g:i A|after:start_time'
        ], [
            'class_id.required' => 'Please select a class',
            'class_id.exists' => 'Selected class does not exist',
            'teacher_id.required' => 'Please select a teacher',
            'teacher_id.exists' => 'Selected teacher does not exist',
            'room_id.required' => 'Please select a room',
            'room_id.exists' => 'Selected room does not exist',
            'subject_id.required' => 'Please select a subject',
            'subject_id.exists' => 'Selected subject does not exist',
            'weekday.required' => 'Day is required',
            'weekday.integer' => 'Day must be a valid number',
            'weekday.between' => 'Day must be between 1 and 7',
            'start_time.required' => 'Please enter a start time',
            'start_time.date_format' => 'Start time must be in valid format (e.g., 8:00 AM)',
            'end_time.required' => 'Please enter an end time',
            'end_time.date_format' => 'End time must be in valid format (e.g., 10:00 AM)',
            'end_time.after' => 'End time must be after start time'
        ]);
        
        return $validator;
    }
    
    /**
     * Check for scheduling conflicts (internal method)
     */
    private function checkConflictsInternal(array $data, $excludeId = null)
    {
        $conflicts = [];
        
        try {
            // Convert time format for comparison
            $startTime = Carbon::createFromFormat('g:i A', $data['start_time'])->format('H:i:s');
            $endTime = Carbon::createFromFormat('g:i A', $data['end_time'])->format('H:i:s');
            
            // Check teacher conflicts
            $teacherQuery = Lesson::with('class', 'subject', 'room', 'teacher')
                ->where('teacher_id', $data['teacher_id'])
                ->where('weekday', $data['weekday'])
                ->where(function($query) use ($startTime, $endTime) {
                    $query->where(function($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $endTime)
                          ->where('end_time', '>', $startTime);
                    });
                });
            
            if ($excludeId) {
                $teacherQuery->where('id', '!=', $excludeId);
            }
            
            $teacherConflicts = $teacherQuery->get();
            if ($teacherConflicts->count() > 0) {
                $teacherName = $this->getTeacherName($data['teacher_id']);
                $conflicts[] = [
                    'type' => 'teacher',
                    'message' => "Teacher {$teacherName} is already scheduled during this time",
                    'conflicting_lessons' => $teacherConflicts->map(function($lesson) {
                        return [
                            'id' => $lesson->id,
                            'class' => $lesson->class->display_name ?? 'Unknown Class',
                            'subject' => $lesson->subject->name ?? 'Unknown Subject',
                            'teacher' => $this->getTeacherNameFromLesson($lesson),
                            'room' => $lesson->room->name ?? 'Unknown Room',
                            'time' => $this->formatTimeRange($lesson->start_time, $lesson->end_time)
                        ];
                    })
                ];
            }
            
            // Check room conflicts
            $roomQuery = Lesson::with('class', 'teacher', 'subject', 'room')
                ->where('room_id', $data['room_id'])
                ->where('weekday', $data['weekday'])
                ->where(function($query) use ($startTime, $endTime) {
                    $query->where(function($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $endTime)
                          ->where('end_time', '>', $startTime);
                    });
                });
            
            if ($excludeId) {
                $roomQuery->where('id', '!=', $excludeId);
            }
            
            $roomConflicts = $roomQuery->get();
            if ($roomConflicts->count() > 0) {
                $roomName = $this->getRoomName($data['room_id']);
                $conflicts[] = [
                    'type' => 'room',
                    'message' => "Room {$roomName} is already occupied during this time",
                    'conflicting_lessons' => $roomConflicts->map(function($lesson) {
                        return [
                            'id' => $lesson->id,
                            'class' => $lesson->class->display_name ?? 'Unknown Class',
                            'teacher' => $this->getTeacherNameFromLesson($lesson),
                            'subject' => $lesson->subject->name ?? 'Unknown Subject',
                            'room' => $lesson->room->name ?? 'Unknown Room',
                            'time' => $this->formatTimeRange($lesson->start_time, $lesson->end_time)
                        ];
                    })
                ];
            }
            
            // Check class conflicts
            $classQuery = Lesson::with('teacher', 'subject', 'room', 'class')
                ->where('class_id', $data['class_id'])
                ->where('weekday', $data['weekday'])
                ->where(function($query) use ($startTime, $endTime) {
                    $query->where(function($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $endTime)
                          ->where('end_time', '>', $startTime);
                    });
                });
            
            if ($excludeId) {
                $classQuery->where('id', '!=', $excludeId);
            }
            
            $classConflicts = $classQuery->get();
            if ($classConflicts->count() > 0) {
                $className = $this->getClassName($data['class_id']);
                $conflicts[] = [
                    'type' => 'class',
                    'message' => "Class {$className} is already scheduled during this time",
                    'conflicting_lessons' => $classConflicts->map(function($lesson) {
                        return [
                            'id' => $lesson->id,
                            'class' => $lesson->class->display_name ?? 'Unknown Class',
                            'subject' => $lesson->subject->name ?? 'Unknown Subject',
                            'teacher' => $this->getTeacherNameFromLesson($lesson),
                            'room' => $lesson->room->name ?? 'Unknown Room',
                            'time' => $this->formatTimeRange($lesson->start_time, $lesson->end_time)
                        ];
                    })
                ];
            }
            
        } catch (\Exception $e) {
            $conflicts[] = [
                'type' => 'error',
                'message' => 'Error checking conflicts: ' . $e->getMessage()
            ];
        }
        
        return $conflicts;
    }

    /**
     * Get teacher name with fallback
     */
    private function getTeacherName($teacherId)
    {
        if (!$teacherId) {
            return 'Unknown Teacher';
        }
        
        $teacher = User::find($teacherId);
        return $teacher ? $teacher->name : 'Unknown Teacher';
    }

    /**
     * Get teacher name from lesson with fallback
     */
    private function getTeacherNameFromLesson($lesson)
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
     * Get room name with fallback
     */
    private function getRoomName($roomId)
    {
        if (!$roomId) {
            return 'Unknown Room';
        }
        
        $room = Room::find($roomId);
        return $room ? ($room->display_name ?? $room->name) : 'Unknown Room';
    }

    /**
     * Get class name with fallback
     */
    private function getClassName($classId)
    {
        if (!$classId) {
            return 'Unknown Class';
        }
        
        $class = SchoolClass::find($classId);
        return $class ? ($class->display_name ?? $class->name) : 'Unknown Class';
    }

    /**
     * Format time range for display
     */
    private function formatTimeRange($startTime, $endTime)
    {
        try {
            $start = Carbon::createFromFormat('H:i:s', $startTime);
            $end = Carbon::createFromFormat('H:i:s', $endTime);
            
            return $start->format('g:i A') . ' - ' . $end->format('g:i A');
        } catch (\Exception $e) {
            return $startTime . ' - ' . $endTime;
        }
    }
}
