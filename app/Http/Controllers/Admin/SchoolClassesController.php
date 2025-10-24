<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroySchoolClassRequest;
use App\Http\Requests\StoreSchoolClassRequest;
use App\Http\Requests\UpdateSchoolClassRequest;
use App\SchoolClass;
use App\AcademicProgram;
use App\GradeLevel;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SchoolClassesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('school_class_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $academicPrograms = AcademicProgram::active()
            ->with(['gradeLevels' => function($query) {
                $query->active()->ordered();
            }])
            ->get();

        // Strictly scope visible classes per program/type
        $academicPrograms->each(function($program) {
            $classesQuery = SchoolClass::query()
                ->where('program_id', $program->id)
                ->where('is_active', true)
                ->with(['gradeLevel']);

            if ($program->type === 'senior_high') {
                $classesQuery->whereNotNull('grade_level_id')
                    ->whereHas('gradeLevel', function($q) use ($program) {
                        $q->where('program_id', $program->id)->where('is_active', true);
                    });
            } else {
                // College & Diploma: include classes tied to this program; allow null grade_level_id but prefer matching grade levels
                $classesQuery->where(function($q) use ($program) {
                    $q->whereNull('grade_level_id')
                      ->orWhereHas('gradeLevel', function($qq) use ($program) {
                          $qq->where('program_id', $program->id);
                      });
                });
            }

            $program->setRelation('schoolClasses', $classesQuery->orderBy('name')->get());
        });

        return view('admin.school-classes.index', compact('academicPrograms'));
    }

    public function byProgram($programType, Request $request)
    {
        abort_if(Gate::denies('school_class_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Validate program type
        if (!in_array($programType, ['senior_high', 'diploma', 'college'])) {
            abort(404, 'Invalid program type');
        }

        // Get programs of the specified type (there should be multiple programs per type)
        $programs = AcademicProgram::where('type', $programType)->active()->get();
        
        if ($programs->isEmpty()) {
            // Show empty page instead of 404 error
            $gradeLevels = collect();
            $schoolClasses = collect();
            $search = '';
            $programId = null;
            $noMatches = false;
            return view('admin.school-classes.by-program', compact('programs', 'programType', 'gradeLevels', 'schoolClasses', 'search', 'programId', 'noMatches'));
        }

        // For now, get all grade levels and classes for all programs of this type
        $gradeLevels = GradeLevel::whereIn('program_id', $programs->pluck('id'))
            ->active()
            ->ordered()
            ->with('program')
            ->get();
            
        $classQuery = SchoolClass::whereIn('program_id', $programs->pluck('id'))
            ->where('is_active', true)
            ->with(['gradeLevel', 'program', 'lessons.subject', 'lessons.teacher', 'lessons.room']);

        // Strict scoping to avoid cross-program leakage
        $classQuery->whereHas('program', function($q) use ($programType) {
            $q->where('type', $programType)->where('is_active', true);
        });
        if ($programType === 'senior_high') {
            $classQuery->whereNotNull('grade_level_id')
                ->whereHas('gradeLevel', function($q) use ($programs) {
                    $q->whereIn('program_id', $programs->pluck('id'))->where('is_active', true);
                });
        }

        $search = trim((string) $request->get('q', ''));
        $programId = $request->get('program_id');

        if ($search !== '') {
            $classQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('section', 'like', "%{$search}%");
            });
        }
        if (!empty($programId)) {
            $classQuery->where('program_id', $programId);
        }
        $schoolClasses = $classQuery->orderBy('name')->get()->groupBy(['program_id', 'grade_level_id']);

        // Flag no results for UI alert when searching
        $noMatches = ($search !== '' || !empty($programId)) && $schoolClasses->flatten(2)->count() === 0;

        return view('admin.school-classes.by-program', compact('programs', 'programType', 'gradeLevels', 'schoolClasses', 'search', 'programId', 'noMatches'));
    }

    public function byProgramAndGrade($programType, $gradeLevelId)
    {
        abort_if(Gate::denies('school_class_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Validate program type
        if (!in_array($programType, ['senior_high', 'diploma', 'college'])) {
            abort(404, 'Invalid program type');
        }

        // Find the specific grade level
        $gradeLevel = GradeLevel::where('id', $gradeLevelId)
            ->whereHas('program', function($query) use ($programType) {
                $query->where('type', $programType);
            })
            ->with('program')
            ->first();
        
        if (!$gradeLevel) {
            abort(404, 'Grade Level not found');
        }

        $program = $gradeLevel->program;

        $schoolClasses = SchoolClass::where('program_id', $program->id)
            ->where('grade_level_id', $gradeLevel->id)
            ->with(['gradeLevel', 'program'])
            ->get();

        return view('admin.school-classes.by-grade', compact('program', 'gradeLevel', 'schoolClasses'));
    }

    public function manageProgram($programId)
    {
        abort_if(Gate::denies('school_class_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Find the specific program
        $program = AcademicProgram::where('id', $programId)->active()->first();
        
        if (!$program) {
            abort(404, 'Program not found');
        }

        // Get grade levels for this specific program
        $gradeLevels = GradeLevel::where('program_id', $program->id)
            ->active()
            ->ordered()
            ->with('program')
            ->get();
            
        // Get school classes for this specific program
        $schoolClasses = SchoolClass::where('program_id', $program->id)
            ->with(['gradeLevel', 'program'])
            ->get()
            ->groupBy('grade_level_id');

        return view('admin.school-classes.manage-program', compact('program', 'gradeLevels', 'schoolClasses'));
    }

    public function create(Request $request)
    {
        abort_if(Gate::denies('school_class_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $preSelectedProgramId = $request->get('program_id');
        $preSelectedGradeLevelId = $request->get('grade_level_id');
        
        $academicPrograms = AcademicProgram::active()->get();
        $gradeLevels = collect();
        
        if ($preSelectedProgramId) {
            $gradeLevels = GradeLevel::where('program_id', $preSelectedProgramId)
                ->active()
                ->ordered()
                ->get();
        }

        return view('admin.school-classes.create', compact('academicPrograms', 'preSelectedProgramId', 'preSelectedGradeLevelId', 'gradeLevels'));
    }

    public function store(StoreSchoolClassRequest $request)
    {
        $data = $request->all();
        
        // Handle grade level assignment based on program type
        $program = AcademicProgram::find($data['program_id']);
        if (!$program) {
            return back()->withErrors(['error' => 'Invalid program selected']);
        }
        
        if ($program->type === 'senior_high') {
            // For senior high school, grade level is required and provided by user
            // Validate that grade level belongs to the program
            if (!empty($data['grade_level_id'])) {
                $gradeLevel = GradeLevel::where('id', $data['grade_level_id'])
                    ->where('program_id', $program->id)
                    ->first();
                if (!$gradeLevel) {
                    return back()->withErrors(['error' => 'Invalid grade level for selected program']);
                }
            }
        } else {
            // For college programs, auto-assign the first grade level if not provided
            if (empty($data['grade_level_id'])) {
                $firstGradeLevel = $program->gradeLevels()->active()->ordered()->first();
                if ($firstGradeLevel) {
                    $data['grade_level_id'] = $firstGradeLevel->id;
                }
            } else {
                // Validate that grade level belongs to the program
                $gradeLevel = GradeLevel::where('id', $data['grade_level_id'])
                    ->where('program_id', $program->id)
                    ->first();
                if (!$gradeLevel) {
                    return back()->withErrors(['error' => 'Invalid grade level for selected program']);
                }
            }
        }
        
        // Ensure required fields have default values
        $data['is_active'] = $data['is_active'] ?? true;
        
        try {
            $schoolClass = SchoolClass::create($data);
            
            // Redirect back to the appropriate page based on context
            if ($request->has('program_id') && $request->has('grade_level_id')) {
                $program = AcademicProgram::find($request->get('program_id'));
                if ($program) {
                    return redirect()->route('admin.school-classes.program.grade', [
                        $program->type, 
                        $request->get('grade_level_id')
                    ])->with('success', 'Class created successfully!');
                }
            }
            
            // If we have program context but no grade level, go to program view
            if ($request->has('program_id')) {
                $program = AcademicProgram::find($request->get('program_id'));
                if ($program) {
                    return redirect()->route('admin.school-classes.program', $program->type)
                        ->with('success', 'Class created successfully!');
                }
            }
            
            return redirect()->route('admin.school-classes.index')->with('success', 'Class created successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create class: ' . $e->getMessage()]);
        }
    }

    public function edit(Request $request, SchoolClass $schoolClass)
    {
        abort_if(Gate::denies('school_class_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $preSelectedProgramId = $request->get('program_id');
        $preSelectedGradeLevelId = $request->get('grade_level_id');
        
        $academicPrograms = AcademicProgram::active()->get();
        $gradeLevels = GradeLevel::where('program_id', $schoolClass->program_id)
            ->active()
            ->ordered()
            ->get();

        return view('admin.school-classes.edit', compact(
            'schoolClass', 
            'academicPrograms', 
            'gradeLevels', 
            'preSelectedProgramId', 
            'preSelectedGradeLevelId'
        ));
    }

    public function update(UpdateSchoolClassRequest $request, SchoolClass $schoolClass)
    {
        $data = $request->all();
        
        // Handle grade level assignment based on program type
        $program = AcademicProgram::find($data['program_id']);
        if (!$program) {
            return back()->withErrors(['error' => 'Invalid program selected']);
        }
        
        if ($program->type === 'senior_high') {
            // For senior high school, grade level is required and provided by user
            // Validate that grade level belongs to the program
            if (!empty($data['grade_level_id'])) {
                $gradeLevel = GradeLevel::where('id', $data['grade_level_id'])
                    ->where('program_id', $program->id)
                    ->first();
                if (!$gradeLevel) {
                    return back()->withErrors(['error' => 'Invalid grade level for selected program']);
                }
            }
        } else {
            // For college programs, auto-assign the first grade level if not provided
            if (empty($data['grade_level_id'])) {
                $firstGradeLevel = $program->gradeLevels()->active()->ordered()->first();
                if ($firstGradeLevel) {
                    $data['grade_level_id'] = $firstGradeLevel->id;
                }
            } else {
                // Validate that grade level belongs to the program
                $gradeLevel = GradeLevel::where('id', $data['grade_level_id'])
                    ->where('program_id', $program->id)
                    ->first();
                if (!$gradeLevel) {
                    return back()->withErrors(['error' => 'Invalid grade level for selected program']);
                }
            }
        }
        
        // Ensure required fields have default values
        $data['is_active'] = $data['is_active'] ?? true;
        
        try {
            $schoolClass->update($data);
            
            // Redirect back to the appropriate page based on context
            if ($request->has('program_id') && $request->has('grade_level_id')) {
                $program = AcademicProgram::find($request->get('program_id'));
                if ($program) {
                    return redirect()->route('admin.school-classes.program.grade', [
                        $program->type, 
                        $request->get('grade_level_id')
                    ])->with('success', 'Class updated successfully!');
                }
            }
            
            // If we have program context but no grade level, go to program view
            if ($request->has('program_id')) {
                $program = AcademicProgram::find($request->get('program_id'));
                if ($program) {
                    return redirect()->route('admin.school-classes.program', $program->type)
                        ->with('success', 'Class updated successfully!');
                }
            }
            
            return redirect()->route('admin.school-classes.index')->with('success', 'Class updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update class: ' . $e->getMessage()]);
        }
    }

    public function show(SchoolClass $schoolClass)
    {
        abort_if(Gate::denies('school_class_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $schoolClass->load('classLessons', 'classUsers');
        
        // Get lessons for this specific class
        $lessons = \App\Lesson::with(['teacher', 'room', 'subject'])
            ->where('class_id', $schoolClass->id)
            ->orderBy('weekday')
            ->orderBy('start_time')
            ->get();
            
        $weekDays = \App\Lesson::WEEK_DAYS;
        
        // Organize lessons by weekday for timetable display
        $calendarData = [];
        foreach ($weekDays as $dayIndex => $dayName) {
            $calendarData[$dayIndex] = [];
        }
        
        foreach ($lessons as $lesson) {
            $calendarData[$lesson->weekday][] = [
                'id' => $lesson->id,
                'class_name' => $schoolClass->name,
                'teacher_name' => $lesson->teacher->name ?? 'No Teacher',
                'room_name' => $lesson->room->name ?? 'No Room',
                'subject_name' => $lesson->subject->name ?? 'No Subject',
                'subject_code' => $lesson->subject->code ?? '',
                'start_time' => $lesson->start_time,
                'end_time' => $lesson->end_time,
                'weekday' => $lesson->weekday,
            ];
        }

        return view('admin.school-classes.show', compact('schoolClass', 'lessons', 'weekDays', 'calendarData'));
    }

    public function destroy(SchoolClass $schoolClass)
    {
        abort_if(Gate::denies('school_class_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Check if there are students enrolled in this class
        $enrolledStudents = $schoolClass->classUsers()->where('is_student', true)->count();
        if ($enrolledStudents > 0) {
            return back()->withErrors([
                'error' => "Cannot delete class '{$schoolClass->name}' because it has {$enrolledStudents} enrolled student(s). Please reassign or remove the students first."
            ]);
        }

        // Check if there are active lessons for this class
        $activeLessons = $schoolClass->lessons()->count();
        if ($activeLessons > 0) {
            return back()->withErrors([
                'error' => "Cannot delete class '{$schoolClass->name}' because it has {$activeLessons} scheduled lesson(s). Please remove the lessons first."
            ]);
        }

        try {
            // Hard delete the class - completely remove from database
            $schoolClass->forceDelete();
            return back()->with('success', "Class '{$schoolClass->name}' has been successfully deleted.");
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return back()->withErrors([
                    'error' => "Cannot delete class '{$schoolClass->name}' because it has associated data. Please remove all related records first."
                ]);
            }
            return back()->withErrors([
                'error' => 'An error occurred while deleting the class. Please try again.'
            ]);
        }
    }

    public function massDestroy(MassDestroySchoolClassRequest $request)
    {
        $classIds = request('ids');
        $errors = [];
        $deletedCount = 0;

        foreach ($classIds as $classId) {
            $schoolClass = SchoolClass::find($classId);
            if (!$schoolClass) {
                continue;
            }

            // Check if there are students enrolled in this class
            $enrolledStudents = $schoolClass->classUsers()->where('is_student', true)->count();
            if ($enrolledStudents > 0) {
                $errors[] = "Cannot delete class '{$schoolClass->name}' because it has {$enrolledStudents} enrolled student(s).";
                continue;
            }

            // Check if there are active lessons for this class
            $activeLessons = $schoolClass->lessons()->count();
            if ($activeLessons > 0) {
                $errors[] = "Cannot delete class '{$schoolClass->name}' because it has {$activeLessons} scheduled lesson(s).";
                continue;
            }

            try {
                // Hard delete the class - completely remove from database
                $schoolClass->forceDelete();
                $deletedCount++;
            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() == '23000') {
                    $errors[] = "Cannot delete class '{$schoolClass->name}' because it has associated data.";
                } else {
                    $errors[] = "An error occurred while deleting class '{$schoolClass->name}'.";
                }
            }
        }

        if (!empty($errors)) {
            return response()->json([
                'message' => 'Some classes could not be deleted.',
                'errors' => $errors,
                'deleted_count' => $deletedCount,
                'total_count' => count($classIds)
            ], Response::HTTP_CONFLICT);
        }

        return response()->json([
            'message' => "Successfully deleted {$deletedCount} class(es).",
            'deleted_count' => $deletedCount
        ], Response::HTTP_OK);
    }

    /**
     * Get program type for a class (AJAX)
     * Used for dynamic weekday dropdown
     */
    public function getProgramType(SchoolClass $schoolClass)
    {
        abort_if(Gate::denies('school_class_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response()->json([
            'program_type' => $schoolClass->program->type ?? null,
            'program_name' => $schoolClass->program->name ?? 'N/A',
        ]);
    }
}
