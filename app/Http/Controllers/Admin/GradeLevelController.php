<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\GradeLevel;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GradeLevelController extends Controller
{
    public function byProgram($programId)
    {
        abort_if(Gate::denies('grade_level_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $gradeLevels = GradeLevel::where('program_id', $programId)
            ->active()
            ->ordered()
            ->get(['id', 'level_name']);

        return response()->json($gradeLevels);
    }
}