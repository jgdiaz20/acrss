<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyLessonRequest;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Lesson;
use App\Room;
use App\SchoolClass;
use App\Services\CacheInvalidationService;
use App\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LessonsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('lesson_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Handle clear filters request
        if ($request->has('clear_filters')) {
            $request->session()->forget('admin_lessons_filters');
            return redirect()->route('admin.lessons.index');
        }

        // Get filters from request or session
        $filters = $request->only(['class_id', 'teacher_id', 'subject_id', 'weekday', 'search', 'per_page']);
        
        // Store filters in session if any are provided
        if (!empty(array_filter($filters))) {
            $request->session()->put('admin_lessons_filters', $filters);
        } elseif (!$request->has('page')) {
            // Load from session if no filters in request and not paginating
            $filters = $request->session()->get('admin_lessons_filters', []);
        }

        // Build query
        $query = Lesson::with(['class', 'teacher', 'room', 'subject']);

        // Apply filters
        if (!empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (!empty($filters['teacher_id'])) {
            $query->where('teacher_id', $filters['teacher_id']);
        }

        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if (!empty($filters['weekday'])) {
            $query->where('weekday', $filters['weekday']);
        }

        // Apply search
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            
            // Collect weekday keys that match the search term
            $matchingWeekdays = [];
            foreach (Lesson::WEEK_DAYS as $key => $day) {
                if (stripos($day, $searchTerm) !== false) {
                    $matchingWeekdays[] = $key;
                }
            }
            
            $query->where(function($q) use ($searchTerm, $matchingWeekdays) {
                // Search in class name
                $q->whereHas('class', function($subQuery) use ($searchTerm) {
                    $subQuery->where('name', 'like', "%{$searchTerm}%");
                });
                
                // OR search in teacher name
                $q->orWhereHas('teacher', function($subQuery) use ($searchTerm) {
                    $subQuery->where('name', 'like', "%{$searchTerm}%");
                });
                
                // OR search in subject name
                $q->orWhereHas('subject', function($subQuery) use ($searchTerm) {
                    $subQuery->where('name', 'like', "%{$searchTerm}%");
                });
                
                // OR search in room name or description
                $q->orWhereHas('room', function($subQuery) use ($searchTerm) {
                    $subQuery->where('name', 'like', "%{$searchTerm}%")
                             ->orWhere('description', 'like', "%{$searchTerm}%");
                });
                
                // OR search by weekday name
                if (!empty($matchingWeekdays)) {
                    $q->orWhereIn('weekday', $matchingWeekdays);
                }
            });
        }

        // Get per page value
        $perPage = !empty($filters['per_page']) ? (int)$filters['per_page'] : 20;
        
        // Validate per page value
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 20;
        }

        // Order and paginate
        $lessons = $query->orderBy('weekday')
                         ->orderBy('start_time')
                         ->paginate($perPage)
                         ->appends($filters);

        // Get filter data for dropdowns
        $classes = SchoolClass::where('is_active', true)
                             ->with(['program', 'gradeLevel'])
                             ->get()
                             ->mapWithKeys(function($class) {
                                 return [$class->id => $class->display_name];
                             });

        $teachers = User::where('is_teacher', true)
                       ->orderBy('name')
                       ->pluck('name', 'id');

        $subjects = \App\Subject::where('is_active', true)
                               ->orderBy('name')
                               ->pluck('name', 'id');

        return view('admin.lessons.index', compact('lessons', 'classes', 'teachers', 'subjects', 'filters', 'perPage'));
    }

    public function create()
    {
        abort_if(Gate::denies('lesson_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $classes = SchoolClass::where('is_active', true)->with(['program', 'gradeLevel'])->get()->mapWithKeys(function($class) {
            return [$class->id => $class->display_name];
        })->prepend(trans('global.pleaseSelect'), '');

        $teachers = User::where('is_teacher', true)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $rooms = Room::all()->pluck('display_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        // Get full subject objects for scheduling_mode access in JavaScript
        $subjectsCollection = \App\Subject::where('is_active', true)->get();
        $subjects = $subjectsCollection->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.lessons.create', compact('classes', 'teachers', 'rooms', 'subjects', 'subjectsCollection'));
    }

    public function store(StoreLessonRequest $request)
    {
        $data = $request->all();
        
        // Calculate duration from start and end time
        $duration = Lesson::calculateDuration($data['start_time'], $data['end_time']);
        $data['duration_hours'] = $duration;
        
        // Get subject and validate lesson type
        $subject = \App\Subject::findOrFail($data['subject_id']);
        $lessonType = $data['lesson_type'];
        $classId = $data['class_id'];
        
        // STRICT VALIDATION: Lesson type must match subject mode
        if ($subject->scheduling_mode === 'lab' && $lessonType !== 'laboratory') {
            return back()->withErrors([
                'lesson_type' => 'This subject is in Lab mode and only allows Laboratory lessons.'
            ])->withInput();
        }
        
        if ($subject->scheduling_mode === 'lecture' && $lessonType !== 'lecture') {
            return back()->withErrors([
                'lesson_type' => 'This subject is in Lecture mode and only allows Lecture lessons.'
            ])->withInput();
        }
        
        // STRICT VALIDATION: Duration enforcement
        if ($lessonType === 'laboratory') {
            if ($duration < 3.0) {
                return back()->withErrors([
                    'end_time' => 'Laboratory lessons must be at least 3 hours. Please adjust the end time.'
                ])->withInput();
            }
            if ($duration > 5.0) {
                return back()->withErrors([
                    'end_time' => 'Laboratory lessons cannot exceed 5 hours. Please adjust the end time.'
                ])->withInput();
            }
        }
        
        if ($lessonType === 'lecture') {
            if ($duration < 1.0) {
                return back()->withErrors([
                    'end_time' => 'Lecture lessons must be at least 1 hour. Please adjust the end time.'
                ])->withInput();
            }
            if ($duration > 3.0) {
                return back()->withErrors([
                    'end_time' => 'Lecture lessons cannot exceed 3 hours. Please adjust the end time.'
                ])->withInput();
            }
        }
        
        // STRICT VALIDATION: Check if this would exceed total hours for this class
        $scheduledHours = $subject->getScheduledHoursByClass($classId);
        $totalRequired = $subject->total_hours;
        
        if (($scheduledHours + $duration) > $totalRequired) {
            $remaining = $subject->getRemainingHoursByClass($classId);
            return back()->withErrors([
                'duration_hours' => "This lesson would exceed the total required hours for this subject and class. Remaining hours: {$remaining}h of {$totalRequired}h total."
            ])->withInput();
        }
        
        // STRICT VALIDATION: Check specific lesson type hours
        if ($lessonType === 'lecture') {
            $scheduledLectureHours = $subject->getScheduledLectureHoursByClass($classId);
            if (($scheduledLectureHours + $duration) > $subject->total_lecture_hours) {
                $remaining = $subject->getRemainingLectureHoursByClass($classId);
                return back()->withErrors([
                    'duration_hours' => "This would exceed the total lecture hours for this subject and class. Remaining lecture hours: {$remaining}h of {$subject->total_lecture_hours}h total."
                ])->withInput();
            }
        }
        
        if ($lessonType === 'laboratory') {
            $scheduledLabHours = $subject->getScheduledLabHoursByClass($classId);
            if (($scheduledLabHours + $duration) > $subject->total_lab_hours) {
                $remaining = $subject->getRemainingLabHoursByClass($classId);
                return back()->withErrors([
                    'duration_hours' => "This would exceed the total laboratory hours for this subject and class. Remaining lab hours: {$remaining}h of {$subject->total_lab_hours}h total."
                ])->withInput();
            }
        }
        
        $lesson = Lesson::create($data);
        
        // Clear lesson-related caches after creating a new lesson
        CacheInvalidationService::clearLessonCaches();

        return redirect()->route('admin.lessons.index')->with('success', 'Schedule created successfully!');
    }

    public function edit(Lesson $lesson)
    {
        abort_if(Gate::denies('lesson_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $classes = SchoolClass::where('is_active', true)->with(['program', 'gradeLevel'])->get()->mapWithKeys(function($class) {
            return [$class->id => $class->display_name];
        })->prepend(trans('global.pleaseSelect'), '');

        $teachers = User::where('is_teacher', true)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $rooms = Room::all()->pluck('display_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $subjects = \App\Subject::where('is_active', true)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $lesson->load('class', 'teacher', 'room', 'subject');

        return view('admin.lessons.edit', compact('classes', 'teachers', 'rooms', 'subjects', 'lesson'));
    }

    public function update(UpdateLessonRequest $request, Lesson $lesson)
    {
        $data = $request->all();
        
        // Recalculate duration from start and end time
        $duration = Lesson::calculateDuration($data['start_time'], $data['end_time']);
        $data['duration_hours'] = $duration;
        
        // Get subject and validate lesson type
        $subject = \App\Subject::findOrFail($data['subject_id']);
        $lessonType = $data['lesson_type'];
        $classId = $data['class_id'];
        
        // STRICT VALIDATION: Lesson type must match subject mode
        if ($subject->scheduling_mode === 'lab' && $lessonType !== 'laboratory') {
            return back()->withErrors([
                'lesson_type' => 'This subject is in Lab mode and only allows Laboratory lessons.'
            ])->withInput();
        }
        
        if ($subject->scheduling_mode === 'lecture' && $lessonType !== 'lecture') {
            return back()->withErrors([
                'lesson_type' => 'This subject is in Lecture mode and only allows Lecture lessons.'
            ])->withInput();
        }
        
        // STRICT VALIDATION: Duration enforcement
        if ($lessonType === 'laboratory') {
            if ($duration < 3.0) {
                return back()->withErrors([
                    'end_time' => 'Laboratory lessons must be at least 3 hours. Please adjust the end time.'
                ])->withInput();
            }
            if ($duration > 5.0) {
                return back()->withErrors([
                    'end_time' => 'Laboratory lessons cannot exceed 5 hours. Please adjust the end time.'
                ])->withInput();
            }
        }
        
        if ($lessonType === 'lecture') {
            if ($duration < 1.0) {
                return back()->withErrors([
                    'end_time' => 'Lecture lessons must be at least 1 hour. Please adjust the end time.'
                ])->withInput();
            }
            if ($duration > 3.0) {
                return back()->withErrors([
                    'end_time' => 'Lecture lessons cannot exceed 3 hours. Please adjust the end time.'
                ])->withInput();
            }
        }
        
        // STRICT VALIDATION: Check if this would exceed total hours for this class
        // Exclude current lesson's hours from calculation
        $scheduledHours = $subject->getScheduledHoursByClass($classId) - $lesson->duration_hours;
        $totalRequired = $subject->total_hours;
        
        if (($scheduledHours + $duration) > $totalRequired) {
            $remaining = max(0, $totalRequired - $scheduledHours);
            return back()->withErrors([
                'duration_hours' => "This lesson would exceed the total required hours for this subject and class. Remaining hours: {$remaining}h of {$totalRequired}h total."
            ])->withInput();
        }
        
        // STRICT VALIDATION: Check specific lesson type hours
        if ($lessonType === 'lecture') {
            $scheduledLectureHours = $subject->getScheduledLectureHoursByClass($classId);
            // Exclude current lesson if it was a lecture
            if ($lesson->lesson_type === 'lecture') {
                $scheduledLectureHours -= $lesson->duration_hours;
            }
            
            if (($scheduledLectureHours + $duration) > $subject->total_lecture_hours) {
                $remaining = max(0, $subject->total_lecture_hours - $scheduledLectureHours);
                return back()->withErrors([
                    'duration_hours' => "This would exceed the total lecture hours for this subject and class. Remaining lecture hours: {$remaining}h of {$subject->total_lecture_hours}h total."
                ])->withInput();
            }
        }
        
        if ($lessonType === 'laboratory') {
            $scheduledLabHours = $subject->getScheduledLabHoursByClass($classId);
            // Exclude current lesson if it was a laboratory
            if ($lesson->lesson_type === 'laboratory') {
                $scheduledLabHours -= $lesson->duration_hours;
            }
            
            if (($scheduledLabHours + $duration) > $subject->total_lab_hours) {
                $remaining = max(0, $subject->total_lab_hours - $scheduledLabHours);
                return back()->withErrors([
                    'duration_hours' => "This would exceed the total laboratory hours for this subject and class. Remaining lab hours: {$remaining}h of {$subject->total_lab_hours}h total."
                ])->withInput();
            }
        }
        
        $lesson->update($data);
        
        // Clear lesson-related caches after updating a lesson
        CacheInvalidationService::clearLessonCaches();

        return redirect()->route('admin.lessons.index')->with('success', 'Schedule updated successfully!');
    }

    /**
     * Get class-specific hours tracking for a subject
     */
    public function getHoursTracking(Request $request)
    {
        try {
            $subjectId = $request->input('subject_id');
            $classId = $request->input('class_id');
            $excludeLessonId = $request->input('exclude_lesson_id'); // For edit mode
            
            if (!$subjectId || !$classId) {
                return response()->json(['success' => false, 'error' => 'Missing subject_id or class_id']);
            }
            
            $subject = \App\Subject::find($subjectId);
            if (!$subject) {
                return response()->json(['success' => false, 'error' => 'Subject not found']);
            }
            
            // Pass exclude_lesson_id to methods to exclude current lesson in edit mode
            $scheduledHours = $subject->getScheduledHoursByClass($classId, $excludeLessonId);
            $remainingHours = $subject->getRemainingHoursByClass($classId, $excludeLessonId);
            $progress = $subject->getProgressPercentageByClass($classId, $excludeLessonId);
            
            $scheduledLectureHours = $subject->getScheduledLectureHoursByClass($classId, $excludeLessonId);
            $scheduledLabHours = $subject->getScheduledLabHoursByClass($classId, $excludeLessonId);
            $remainingLectureHours = $subject->getRemainingLectureHoursByClass($classId, $excludeLessonId);
            $remainingLabHours = $subject->getRemainingLabHoursByClass($classId, $excludeLessonId);
            
            return response()->json([
                'success' => true,
                'total_hours' => $subject->total_hours,
                'scheduled_hours' => $scheduledHours,
                'remaining_hours' => $remainingHours,
                'progress' => $progress,
                'lecture_hours' => [
                    'total' => $subject->total_lecture_hours,
                    'scheduled' => $scheduledLectureHours,
                    'remaining' => $remainingLectureHours
                ],
                'lab_hours' => [
                    'total' => $subject->total_lab_hours,
                    'scheduled' => $scheduledLabHours,
                    'remaining' => $remainingLabHours
                ],
                'scheduling_mode' => $subject->scheduling_mode
            ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
              ->header('Pragma', 'no-cache')
              ->header('Expires', '0');
        } catch (\Exception $e) {
            \Log::error('Hours tracking error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Lesson $lesson)
    {
        abort_if(Gate::denies('lesson_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $lesson->load('class', 'teacher', 'room', 'subject');

        return view('admin.lessons.show', compact('lesson'));
    }

    public function destroy(Lesson $lesson)
    {
        abort_if(Gate::denies('lesson_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Store lesson details before deletion for message
        $lessonDetails = $lesson->subject->name ?? 'Lesson';
        $lessonTime = $lesson->start_time . ' - ' . $lesson->end_time;
        
        // Hard delete the lesson - completely remove from database
        $lesson->forceDelete();
        
        // Clear lesson-related caches after deleting a lesson
        CacheInvalidationService::clearLessonCaches();

        return back()->with('success', "{$lessonDetails} ({$lessonTime}) has been successfully deleted!");
    }

    public function massDestroy(MassDestroyLessonRequest $request)
    {
        // Get lesson count before deletion for message
        $lessonCount = Lesson::whereIn('id', request('ids'))->count();
        
        // Hard delete multiple lessons - completely remove from database
        Lesson::whereIn('id', request('ids'))->forceDelete();
        
        // Clear lesson-related caches after mass deleting lessons
        CacheInvalidationService::clearLessonCaches();

        if ($lessonCount === 1) {
            return response()->json(['message' => "1 schedule has been successfully deleted!"], Response::HTTP_OK);
        } else {
            return response()->json(['message' => "{$lessonCount} schedules have been successfully deleted!"], Response::HTTP_OK);
        }
    }

    // AJAX endpoints for dynamic filtering
    public function getTeachersForSubject(Request $request)
    {
        try {
            $subjectId = $request->get('subject_id');
            $clearCache = $request->get('clear_cache', false);
            
            // Clear cache if requested
            if ($clearCache) {
                CacheInvalidationService::clearTeacherAssignmentCaches($subjectId);
            }
            
            // Use caching for better performance
            $cacheKey = "teachers_for_subject_{$subjectId}";
            $teachers = \Cache::remember($cacheKey, 300, function() use ($subjectId) {
                if (!$subjectId) {
                    // If no subject selected, return all teachers
                    return \App\User::where('is_teacher', true)
                        ->pluck('name', 'id')
                        ->toArray();
                }

                // Get teachers assigned to this specific subject
                $assignedTeachers = \App\TeacherSubject::where('subject_id', $subjectId)
                    ->where('is_active', true)
                    ->with('teacher')
                    ->get()
                    ->mapWithKeys(function($assignment) {
                        return [$assignment->teacher->id => $assignment->teacher->name];
                    })
                    ->toArray();

                // If no teachers assigned to this subject, return a message
                if (empty($assignedTeachers)) {
                    return [
                        'no_teachers' => 'Please assign a teacher to this subject first to proceed. Admin needs to verify teacher qualifications.'
                    ];
                }

                return $assignedTeachers;
            });

            return response()->json(['teachers' => $teachers]);
        } catch (\Exception $e) {
            // Log the error and return empty array
            \Log::error('Error in getTeachersForSubject: ' . $e->getMessage());
            return response()->json(['teachers' => []]);
        }
    }

    public function getRoomsForSubject(Request $request)
    {
        try {
            $subjectId = $request->get('subject_id');
            
            // Use caching for better performance
            $cacheKey = "rooms_for_subject_{$subjectId}";
            $rooms = \Cache::remember($cacheKey, 300, function() use ($subjectId) {
                if (!$subjectId) {
                    return Room::all()->mapWithKeys(function($room) {
                        return [$room->id => $room->display_name];
                    })->toArray();
                }

                $subject = \App\Subject::find($subjectId);
                if (!$subject) {
                    return Room::all()->mapWithKeys(function($room) {
                        return [$room->id => $room->display_name];
                    })->toArray();
                }
                // Return all rooms regardless of subject requirements (no filtering)
                return Room::all()->mapWithKeys(function($room) {
                    return [$room->id => $room->display_name];
                })->toArray();
            });

            return response()->json(['rooms' => $rooms]);
        } catch (\Exception $e) {
            // Log the error and return empty array
            \Log::error('Error in getRoomsForSubject: ' . $e->getMessage());
            return response()->json(['rooms' => []]);
        }
    }

    /**
     * Get lesson info for modal display (AJAX)
     */
    public function getInfo(Lesson $lesson)
    {
        abort_if(Gate::denies('lesson_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response()->json([
            'class_name' => $lesson->class->name ?? 'N/A',
            'subject_name' => $lesson->subject->name ?? 'N/A',
            'teacher_name' => $lesson->teacher->name ?? 'N/A',
            'room_name' => $lesson->room->name ?? 'N/A',
            'start_time' => $lesson->start_time,
            'end_time' => $lesson->end_time,
            'weekday' => $lesson->weekday,
            'weekday_name' => Lesson::WEEK_DAYS[$lesson->weekday] ?? 'N/A',
        ]);
    }
}
