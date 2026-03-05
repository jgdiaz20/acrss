<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroySubjectRequest;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Subject;
use App\Services\CacheInvalidationService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubjectsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('subject_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = Subject::query();

        // Add filtering
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Add search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('code', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        $perPage = $request->get('per_page', 10);
        $subjects = $query->withCount(['lessons', 'teachers'])
                 ->orderBy('name')
                 ->paginate($perPage);

        // Add cache-busting headers for dynamic content
        $response = response()->view('admin.subjects.index', compact('subjects'));
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');

        return $response;
    }

    public function create()
    {
        abort_if(Gate::denies('subject_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.subjects.create');
    }

    public function store(StoreSubjectRequest $request)
    {
        $data = $request->all();
        
        // Handle credit system based on scheduling mode
        if ($data['scheduling_mode'] === 'lab') {
            // Lab mode: Pure laboratory (1 credit = 3 hours)
            $credits = (int) $data['credits'];
            
            // Explicit validation: Credits must be between 1 and 3
            if ($credits < 1 || $credits > 3) {
                return back()->withErrors(['credits' => 'Credits must be between 1 and 3'])->withInput();
            }
            
            $data['lecture_units'] = 0;
            $data['lab_units'] = $credits;
            $data['credits'] = $credits;
        } elseif ($data['scheduling_mode'] === 'lecture') {
            // Lecture mode: Pure lecture (1 credit = 1 hour)
            $credits = (int) $data['credits'];
            
            // Explicit validation: Credits must be between 1 and 3
            if ($credits < 1 || $credits > 3) {
                return back()->withErrors(['credits' => 'Credits must be between 1 and 3'])->withInput();
            }
            
            $data['lecture_units'] = $credits;
            $data['lab_units'] = 0;
            $data['credits'] = $credits;
        } else {
            // Flexible mode: Mixed lecture and laboratory
            $lectureUnits = (int) $request->input('lecture_units', 0);
            $labUnits = (int) $request->input('lab_units', 0);
            
            // Validation: Flexible mode requires at least 1 unit of EACH type
            if ($lectureUnits < 1) {
                return back()->withErrors([
                    'lecture_units' => 'Flexible mode requires at least 1 lecture unit'
                ])->withInput();
            }
            
            if ($labUnits < 1) {
                return back()->withErrors([
                    'lab_units' => 'Flexible mode requires at least 1 lab unit'
                ])->withInput();
            }
            
            // Validation: Total credits cannot exceed 3
            $totalCredits = $lectureUnits + $labUnits;
            if ($totalCredits > 3) {
                return back()->withErrors([
                    'lecture_units' => 'Total credits (lecture + lab) cannot exceed 3',
                    'lab_units' => 'Total credits (lecture + lab) cannot exceed 3'
                ])->withInput();
            }
            
            $data['lecture_units'] = $lectureUnits;
            $data['lab_units'] = $labUnits;
            $data['credits'] = $totalCredits;
        }
        
        // Set default values for fields removed from UI
        $data['requires_lab'] = false;
        $data['requires_equipment'] = false;
        $data['equipment_requirements'] = null;
        $data['is_active'] = true; // Always set subjects as active
        
        $subject = Subject::create($data);

        return redirect()->route('admin.subjects.index')
                        ->with('success', 'Subject created successfully!');
    }

    public function edit(Subject $subject)
    {
        abort_if(Gate::denies('subject_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        try {
            $data = $request->all();
            
            // BACKEND VALIDATION: Prevent scheduling mode change if lessons exist
            if ($subject->lessons()->count() > 0 && $data['scheduling_mode'] !== $subject->scheduling_mode) {
                return back()->withErrors([
                    'scheduling_mode' => 'Cannot change scheduling mode when this subject has existing lessons. Please delete all lessons first.'
                ])->withInput();
            }
            
            // BACKEND VALIDATION: Prevent reducing total hours if lessons exist
            if ($subject->lessons()->count() > 0) {
                $currentTotalHours = $subject->total_hours;
                $newTotalHours = 0;
                
                // Calculate new total hours based on mode
                if ($data['scheduling_mode'] === 'lab') {
                    $newTotalHours = (int)$data['credits'] * 3;
                } elseif ($data['scheduling_mode'] === 'lecture') {
                    $newTotalHours = (int)$data['credits'] * 1;
                } else { // flexible
                    $lectureUnits = (int)$request->input('lecture_units', 0);
                    $labUnits = (int)$request->input('lab_units', 0);
                    $newTotalHours = ($lectureUnits * 1) + ($labUnits * 3);
                }
                
                if ($newTotalHours < $currentTotalHours) {
                    return back()->withErrors([
                        'credits' => "Cannot reduce total hours from {$currentTotalHours}h to {$newTotalHours}h when this subject has existing lessons. Current lessons may exceed the new limit."
                    ])->withInput();
                }
            }
            
            // Handle credit system based on scheduling mode
            if ($data['scheduling_mode'] === 'lab') {
                // Lab mode: Pure laboratory (1 credit = 3 hours)
                $credits = (int) $data['credits'];
                
                // Explicit validation: Credits must be between 1 and 3
                if ($credits < 1 || $credits > 3) {
                    return back()->withErrors(['credits' => 'Credits must be between 1 and 3'])->withInput();
                }
                
                $data['lecture_units'] = 0;
                $data['lab_units'] = $credits;
                $data['credits'] = $credits;
            } elseif ($data['scheduling_mode'] === 'lecture') {
                // Lecture mode: Pure lecture (1 credit = 1 hour)
                $credits = (int) $data['credits'];
                
                // Explicit validation: Credits must be between 1 and 3
                if ($credits < 1 || $credits > 3) {
                    return back()->withErrors(['credits' => 'Credits must be between 1 and 3'])->withInput();
                }
                
                $data['lecture_units'] = $credits;
                $data['lab_units'] = 0;
                $data['credits'] = $credits;
            } else {
                // Flexible mode: Mixed lecture and laboratory
                $lectureUnits = (int) $request->input('lecture_units', 0);
                $labUnits = (int) $request->input('lab_units', 0);
                
                // Validation: Flexible mode requires at least 1 unit of EACH type
                if ($lectureUnits < 1) {
                    return back()->withErrors([
                        'lecture_units' => 'Flexible mode requires at least 1 lecture unit'
                    ])->withInput();
                }
                
                if ($labUnits < 1) {
                    return back()->withErrors([
                        'lab_units' => 'Flexible mode requires at least 1 lab unit'
                    ])->withInput();
                }
                
                // Validation: Total credits cannot exceed 3
                $totalCredits = $lectureUnits + $labUnits;
                if ($totalCredits > 3) {
                    return back()->withErrors([
                        'lecture_units' => 'Total credits (lecture + lab) cannot exceed 3',
                        'lab_units' => 'Total credits (lecture + lab) cannot exceed 3'
                    ])->withInput();
                }
                
                $data['lecture_units'] = $lectureUnits;
                $data['lab_units'] = $labUnits;
                $data['credits'] = $totalCredits;
            }
            
            // Set default values for fields removed from UI
            $data['requires_lab'] = false;
            $data['requires_equipment'] = false;
            $data['equipment_requirements'] = null;
            $data['is_active'] = true; // Always keep subjects as active
            
            $subject->update($data);

            return redirect()->route('admin.subjects.show', $subject)
                            ->with('success', 'Subject "' . $subject->name . '" updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Error updating subject: ' . $e->getMessage());
            
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'An error occurred while updating the subject. Please try again.');
        }
    }

    public function show(Subject $subject)
    {
        abort_if(Gate::denies('subject_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Load fresh data from database (bypass any potential caching)
        $subject->load(['lessons.class', 'lessons.teacher', 'lessons.room', 'teachers']);

        // Return JSON for AJAX requests
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'subject' => [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'code' => $subject->code,
                    'credits' => $subject->credits,
                    'scheduling_mode' => $subject->scheduling_mode,
                    'lecture_units' => $subject->lecture_units,
                    'lab_units' => $subject->lab_units,
                    'total_hours' => $subject->total_hours,
                    'total_lecture_hours' => $subject->total_lecture_hours,
                    'total_lab_hours' => $subject->total_lab_hours,
                    'scheduled_hours' => $subject->scheduled_hours,
                    'remaining_hours' => $subject->remaining_hours,
                    'scheduling_progress' => $subject->scheduling_progress,
                ]
            ]);
        }

        // Get statistics with fresh queries
        $stats = [
            'total_lessons' => $subject->lessons()->count(),
            'active_teachers' => $subject->teachers()->wherePivot('is_active', true)->count(),
            'weekly_hours' => $subject->lessons()->get()->sum(function($lesson) {
                return \Carbon\Carbon::parse($lesson->end_time)->diffInMinutes(\Carbon\Carbon::parse($lesson->start_time));
            }) / 60,
        ];

        // Add cache-busting headers for dynamic content
        $response = response()->view('admin.subjects.show', compact('subject', 'stats'));
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
        $response->headers->set('ETag', md5($subject->id . $subject->updated_at . time()));

        return $response;
    }

    public function destroy(Subject $subject)
    {
        abort_if(Gate::denies('subject_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $subjectName = $subject->name; // Store name before deletion for message
        
        // Check if subject has lessons
        if ($subject->lessons()->count() > 0) {
            $lessonCount = $subject->lessons()->count();
            return back()->with('error', "Cannot delete subject '{$subjectName}' because it has {$lessonCount} scheduled lesson(s). Please delete or reassign all lessons first.");
        }

        // Check if subject has assigned teachers
        if ($subject->teacherSubjects()->where('is_active', true)->count() > 0) {
            $assignedTeachers = $subject->teacherSubjects()
                ->with('teacher')
                ->where('is_active', true)
                ->get()
                ->pluck('teacher.name')
                ->implode(', ');
            
            return back()->with('error', "Cannot delete subject '{$subjectName}' because it has assigned teachers: {$assignedTeachers}. Please remove the subject from these teachers first.");
        }

        try {
            // Remove any remaining teacher assignments first (to avoid foreign key constraint)
            $subject->teachers()->detach();
            
            // Clear relevant caches before deletion
            CacheInvalidationService::clearSubjectCaches($subject->id);
            
            // Hard delete the subject - completely remove from database
            $subject->forceDelete();

            return back()->with('success', "Subject '{$subjectName}' has been successfully deleted!");
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle foreign key constraint violations
            if ($e->getCode() == '23000') {
                return back()->with('error', "Cannot delete subject '{$subjectName}' because it has related data (lessons, teacher assignments, etc.). Please remove all related data first.");
            }
            
            // Re-throw other database exceptions
            throw $e;
        }
    }

    public function massDestroy(MassDestroySubjectRequest $request)
    {
        $subjectIds = (array) $request->input('ids', []);
        $subjects = Subject::whereIn('id', $subjectIds)->get();

        // Validate that all requested subjects exist
        if ($subjects->count() !== count($subjectIds)) {
            return response()->json(['error' => 'One or more selected subjects do not exist.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Enforce all-or-nothing: block if any selected subject has lessons or active teachers
        $blocking = [];
        foreach ($subjects as $subject) {
            $subjectName = $subject->name;
            $lessonCount = $subject->lessons()->count();
            $activeTeacherCount = $subject->teacherSubjects()->where('is_active', true)->count();

            if ($lessonCount > 0) {
                $blocking[] = $subjectName . ' (has ' . $lessonCount . ' lesson' . ($lessonCount === 1 ? '' : 's') . ')';
                continue;
            }
            if ($activeTeacherCount > 0) {
                $blocking[] = $subjectName . ' (has assigned teachers)';
                continue;
            }
        }

        if (!empty($blocking)) {
            return response()->json([
                'error' => 'Deletion blocked. The following subjects cannot be deleted: ' . implode(', ', $blocking)
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Proceed with deletion (detach teachers first), then hard delete all
        foreach ($subjects as $subject) {
            $subject->teachers()->detach();
            CacheInvalidationService::clearSubjectCaches($subject->id);
        }

        // Hard delete subjects - completely remove from database
        Subject::whereIn('id', $subjectIds)->forceDelete();

        $count = count($subjectIds);
        return response()->json([
            'message' => $count === 1
                ? "Subject '" . $subjects->first()->name . "' has been successfully deleted!"
                : "{$count} subjects have been successfully deleted!"
        ], Response::HTTP_OK);
    }

    // Teacher assignment methods
    public function assignTeachers(Subject $subject)
    {
        abort_if(Gate::denies('subject_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Get all users who are teachers
        $teachers = \App\User::where('is_teacher', true)->get();
        
        // Load existing teacher assignments for this subject (fresh from database)
        $subject->load('teachers');

        // Add lesson count for each teacher for this subject
        $teacherLessonCounts = [];
        foreach ($teachers as $teacher) {
            $teacherLessonCounts[$teacher->id] = \App\Lesson::where('teacher_id', $teacher->id)
                ->where('subject_id', $subject->id)
                ->count();
        }

        // Add cache-busting headers to prevent browser caching
        $response = response()->view('admin.subjects.assign-teachers', compact('subject', 'teachers', 'teacherLessonCounts'));
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
        $response->headers->set('ETag', md5($subject->id . $subject->updated_at . time()));

        return $response;
    }

    public function updateTeacherAssignments(Request $request, Subject $subject)
    {
        abort_if(Gate::denies('subject_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            // Log the incoming request for debugging
            \Log::info('Teacher assignment update request', [
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'request_data' => $request->all(),
                'has_teachers' => $request->has('teachers'),
                'teachers_count' => $request->has('teachers') ? count($request->teachers) : 0
            ]);

            // Check if this is a request to remove all teachers
            $removeAllTeachers = $request->has('remove_all_teachers') && $request->input('remove_all_teachers') == '1';
            
            // Filter out empty teacher data (unchecked teachers)
            $teachersData = [];
            if ($request->has('teachers') && is_array($request->teachers)) {
                foreach ($request->teachers as $teacherData) {
                    // Only include teachers that have a teacher_id (checked teachers)
                    if (!empty($teacherData['teacher_id'])) {
                        $teachersData[] = $teacherData;
                    }
                }
            }

            // If no teachers are selected and it's not explicitly a "remove all" request, 
            // this might be an error - but we'll allow it to proceed
            if (empty($teachersData) && !$removeAllTeachers) {
                \Log::warning('No teachers selected and not explicitly removing all', [
                    'subject_id' => $subject->id,
                    'request_data' => $request->all()
                ]);
            }

            // Validate only if there are teachers to validate
            if (!empty($teachersData)) {
                $request->merge(['teachers' => $teachersData]);
                
                $request->validate([
                    'teachers' => 'array',
                    'teachers.*.teacher_id' => 'required|integer|exists:users,id',
                ]);
            }

            // Clear existing assignments safely
            try {
                $subject->teachers()->detach();
                \Log::info('Successfully detached all teachers from subject', ['subject_id' => $subject->id]);
            } catch (\Exception $detachError) {
                \Log::error('Error detaching teachers from subject', [
                    'subject_id' => $subject->id,
                    'error' => $detachError->getMessage()
                ]);
                throw $detachError;
            }

            // Assign new teachers
            foreach ($teachersData as $teacherData) {
                try {
                    $subject->teachers()->attach($teacherData['teacher_id'], [
                        'is_active' => true,
                    ]);
                    \Log::info('Successfully attached teacher to subject', [
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacherData['teacher_id']
                    ]);
                } catch (\Illuminate\Database\QueryException $attachError) {
                    // Handle unique constraint violations specifically
                    if ($attachError->getCode() == '23000') {
                        \Log::warning('Teacher-subject relationship already exists, updating instead', [
                            'subject_id' => $subject->id,
                            'teacher_id' => $teacherData['teacher_id']
                        ]);
                        
                        // Update the existing relationship instead
                        $subject->teachers()->updateExistingPivot($teacherData['teacher_id'], [
                            'is_active' => true,
                        ]);
                    } else {
                        throw $attachError;
                    }
                } catch (\Exception $attachError) {
                    \Log::error('Error attaching teacher to subject', [
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacherData['teacher_id'],
                        'error' => $attachError->getMessage()
                    ]);
                    throw $attachError;
                }
            }

            // Clear relevant caches to ensure real-time updates
            CacheInvalidationService::clearTeacherAssignmentCaches($subject->id);

            $message = count($teachersData) > 0 
                ? 'Successfully assigned ' . count($teachersData) . ' teacher(s) to ' . $subject->name . '!'
                : 'All teachers have been removed from ' . $subject->name . '.';

            // Redirect with cache-busting parameters to ensure fresh data
            $redirectUrl = route('admin.subjects.show', $subject) . '?updated=' . time();
            
            return redirect($redirectUrl)->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Error updating teacher assignments', [
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return redirect()->back()
                            ->with('error', 'An error occurred while updating teacher assignments: ' . $e->getMessage())
                            ->withInput();
        }
    }
}
