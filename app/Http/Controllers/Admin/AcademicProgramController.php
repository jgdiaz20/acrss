<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyAcademicProgramRequest;
use App\Http\Requests\StoreAcademicProgramRequest;
use App\Http\Requests\UpdateAcademicProgramRequest;
use App\AcademicProgram;
use App\GradeLevel;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AcademicProgramController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('academic_program_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $search = trim((string) $request->get('q', ''));
        $type = $request->get('type'); // 'college' | 'senior_high' | null
        $subFilter = trim((string) $request->get('sub_filter', '')); // course/strand free-text

        $query = AcademicProgram::query()->with(['gradeLevels']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($type && in_array($type, array_keys(AcademicProgram::PROGRAM_TYPES), true)) {
            $query->where('type', $type);
        }

        if ($subFilter !== '') {
            // Interpret subFilter as course (for college) or strand (for senior high), matching name/code/description
            $query->where(function ($q) use ($subFilter) {
                $q->where('name', 'like', "%{$subFilter}%")
                  ->orWhere('code', 'like', "%{$subFilter}%")
                  ->orWhere('description', 'like', "%{$subFilter}%");
            });
        }

        $academicPrograms = $query->orderBy('name')->get();

        $activeType = $type ?: '';
        $filterLabel = $activeType === 'college' ? 'Course' : ($activeType === 'senior_high' ? 'Strand' : 'Course/Strand');

        return view('admin.academic-programs.index', compact('academicPrograms', 'search', 'activeType', 'subFilter', 'filterLabel'));
    }

    public function create()
    {
        abort_if(Gate::denies('academic_program_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.academic-programs.create');
    }

    public function store(StoreAcademicProgramRequest $request)
    {
        $academicProgram = AcademicProgram::create($request->all());

        // Automatically create grade levels based on program type
        $this->createGradeLevelsForProgram($academicProgram);

        return redirect()->route('admin.academic-programs.index')->with('success', 'Academic program created successfully!');
    }

    public function edit(AcademicProgram $academicProgram)
    {
        abort_if(Gate::denies('academic_program_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $academicProgram->load('gradeLevels');
        
        // Check for weekend lessons if this is a diploma program
        $weekendLessonCount = 0;
        $weekendLessons = collect();
        
        if ($academicProgram->type === 'diploma') {
            $weekendLessons = \App\Lesson::whereHas('class', function($q) use ($academicProgram) {
                $q->where('program_id', $academicProgram->id);
            })
            ->whereIn('weekday', [6, 7])
            ->with(['class', 'subject'])
            ->get();
            
            $weekendLessonCount = $weekendLessons->count();
        }

        return view('admin.academic-programs.edit', compact('academicProgram', 'weekendLessonCount', 'weekendLessons'));
    }

    public function update(UpdateAcademicProgramRequest $request, AcademicProgram $academicProgram)
    {
        $oldType = $academicProgram->type;
        $academicProgram->update($request->all());

        // If program type changed, recreate grade levels
        if ($oldType !== $academicProgram->type) {
            // Delete existing grade levels
            $academicProgram->gradeLevels()->delete();
            
            // Create new grade levels based on new type
            $this->createGradeLevelsForProgram($academicProgram);
        }

        return redirect()->route('admin.academic-programs.index')->with('success', 'Academic program updated successfully!');
    }

    public function show(AcademicProgram $academicProgram)
    {
        abort_if(Gate::denies('academic_program_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $academicProgram->load('gradeLevels', 'schoolClasses');

        return view('admin.academic-programs.show', compact('academicProgram'));
    }

    public function destroy(AcademicProgram $academicProgram)
    {
        abort_if(Gate::denies('academic_program_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $hasClasses = $academicProgram->schoolClasses()->exists();
        if ($hasClasses) {
            $sections = $academicProgram->schoolClasses()
                ->select(['id', 'name', 'section'])
                ->limit(5)
                ->get()
                ->map(function ($c) {
                    return trim($c->name . (isset($c->section) && $c->section !== '' ? ' - Section ' . $c->section : ''));
                })->toArray();

            $listPreview = count($sections) ? (' Examples: ' . implode(', ', $sections) . ( $academicProgram->schoolClasses()->count() > 5 ? '…' : '')) : '';
            return back()->with('error', "Deletion blocked. Program '{$academicProgram->name}' has classes/sections assigned. Please remove or reassign all classes/sections under this program, then try again." . $listPreview);
        }

        try {
            // Remove program's grade levels first to avoid FK issues
            $academicProgram->gradeLevels()->forceDelete();
            // Hard delete program
            $academicProgram->forceDelete();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', "Cannot delete program '{$academicProgram->name}' due to related records. Remove related classes/levels and try again.");
        }

        return back()->with('success', "Program '{$academicProgram->name}' has been permanently deleted.");
    }

    public function massDestroy(MassDestroyAcademicProgramRequest $request)
    {
        $ids = (array) request('ids', []);

        $programs = AcademicProgram::whereIn('id', $ids)->get();
        if ($programs->count() !== count($ids)) {
            return back()->with('error', 'One or more selected programs do not exist.');
        }

        // All-or-nothing: block if any has classes/sections
        $blocked = $programs->filter(function ($p) { return $p->schoolClasses()->exists(); });
        if ($blocked->isNotEmpty()) {
            $names = $blocked->pluck('name')->join(', ');
            return back()->with('error', "Deletion blocked. The following programs have classes/sections assigned: {$names}.");
        }

        // Proceed with hard delete
        foreach ($programs as $program) {
            $program->gradeLevels()->forceDelete();
        }
        AcademicProgram::whereIn('id', $ids)->forceDelete();

        $count = count($ids);
        return back()->with('success', $count === 1 ? '1 program has been permanently deleted.' : "{$count} programs have been permanently deleted.");
    }

    /**
     * Create grade levels for a newly created academic program
     */
    private function createGradeLevelsForProgram(AcademicProgram $program)
    {
        if ($program->type === 'senior_high') {
            // Senior High School Grade Levels (2 years)
            $gradeLevels = [
                [
                    'program_id' => $program->id,
                    'level_name' => 'Grade 11',
                    'level_code' => 'G11',
                    'level_order' => 1,
                    'description' => 'First year of Senior High School',
                    'is_active' => true,
                ],
                [
                    'program_id' => $program->id,
                    'level_name' => 'Grade 12',
                    'level_code' => 'G12',
                    'level_order' => 2,
                    'description' => 'Second year of Senior High School',
                    'is_active' => true,
                ],
            ];
        } elseif ($program->type === 'diploma') {
            // Diploma Program (TESDA) - 3 years
            $gradeLevels = [
                [
                    'program_id' => $program->id,
                    'level_name' => '1st Year',
                    'level_code' => '1Y',
                    'level_order' => 1,
                    'description' => 'First year of Diploma Program',
                    'is_active' => true,
                ],
                [
                    'program_id' => $program->id,
                    'level_name' => '2nd Year',
                    'level_code' => '2Y',
                    'level_order' => 2,
                    'description' => 'Second year of Diploma Program',
                    'is_active' => true,
                ],
                [
                    'program_id' => $program->id,
                    'level_name' => '3rd Year',
                    'level_code' => '3Y',
                    'level_order' => 3,
                    'description' => 'Third year of Diploma Program',
                    'is_active' => true,
                ],
            ];
        } else {
            // College Grade Levels (4 years)
            $gradeLevels = [
                [
                    'program_id' => $program->id,
                    'level_name' => '1st Year',
                    'level_code' => '1Y',
                    'level_order' => 1,
                    'description' => 'First year of College',
                    'is_active' => true,
                ],
                [
                    'program_id' => $program->id,
                    'level_name' => '2nd Year',
                    'level_code' => '2Y',
                    'level_order' => 2,
                    'description' => 'Second year of College',
                    'is_active' => true,
                ],
                [
                    'program_id' => $program->id,
                    'level_name' => '3rd Year',
                    'level_code' => '3Y',
                    'level_order' => 3,
                    'description' => 'Third year of College',
                    'is_active' => true,
                ],
                [
                    'program_id' => $program->id,
                    'level_name' => '4th Year',
                    'level_code' => '4Y',
                    'level_order' => 4,
                    'description' => 'Fourth year of College',
                    'is_active' => true,
                ],
            ];
        }

        // Create the grade levels
        foreach ($gradeLevels as $gradeLevel) {
            GradeLevel::create($gradeLevel);
        }
    }
}