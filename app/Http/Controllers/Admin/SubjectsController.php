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

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('lab')) {
            $query->where('requires_lab', $request->lab);
        }

        if ($request->filled('equipment')) {
            $query->where('requires_equipment', $request->equipment);
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

        $perPage = $request->get('per_page', 20);
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
        
        // Handle checkbox fields - if not present, set to false
        $data['requires_lab'] = $request->has('requires_lab');
        $data['requires_equipment'] = $request->has('requires_equipment');
        $data['is_active'] = $request->has('is_active');
        
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
            
            // Handle checkbox fields - if not present, set to false
            $data['requires_lab'] = $request->has('requires_lab');
            $data['requires_equipment'] = $request->has('requires_equipment');
            $data['is_active'] = $request->has('is_active');
            
            $subject->update($data);

            return redirect()->route('admin.subjects.show', $subject)
                            ->with('success', 'Subject "' . $subject->name . '" updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Error updating subject: ' . $e->getMessage());
            
            return redirect()->back()
                            ->with('error', 'An error occurred while updating the subject. Please try again.')
                            ->withInput();
        }
    }

    public function show(Subject $subject)
    {
        abort_if(Gate::denies('subject_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Load fresh data from database (bypass any potential caching)
        $subject->load(['lessons.class', 'lessons.teacher', 'lessons.room', 'teachers']);

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

        // Add cache-busting headers to prevent browser caching
        $response = response()->view('admin.subjects.assign-teachers', compact('subject', 'teachers'));
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
                    'teachers.*.is_primary' => 'boolean',
                    'teachers.*.experience_years' => 'integer|min:0',
                    'teachers.*.notes' => 'nullable|string|max:500',
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
                        'is_primary' => $teacherData['is_primary'] ?? false,
                        'experience_years' => $teacherData['experience_years'] ?? 0,
                        'notes' => $teacherData['notes'] ?? null,
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
                            'is_primary' => $teacherData['is_primary'] ?? false,
                            'experience_years' => $teacherData['experience_years'] ?? 0,
                            'notes' => $teacherData['notes'] ?? null,
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
