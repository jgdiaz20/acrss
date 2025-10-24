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

        $subjects = \App\Subject::where('is_active', true)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.lessons.create', compact('classes', 'teachers', 'rooms', 'subjects'));
    }

    public function store(StoreLessonRequest $request)
    {
        $lesson = Lesson::create($request->all());
        
        // Clear lesson-related caches after creating a new lesson
        CacheInvalidationService::clearLessonCaches();

        return redirect()->route('admin.lessons.index');
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
        $lesson->update($request->all());
        
        // Clear lesson-related caches after updating a lesson
        CacheInvalidationService::clearLessonCaches();

        return redirect()->route('admin.lessons.index')->with('success', 'Lesson has been successfully updated!');
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

        return back()->with('success', "Lesson '{$lessonDetails}' ({$lessonTime}) has been successfully deleted!");
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
            return response()->json(['message' => "1 lesson has been successfully deleted!"], Response::HTTP_OK);
        } else {
            return response()->json(['message' => "{$lessonCount} lessons have been successfully deleted!"], Response::HTTP_OK);
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
