<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSchoolClassRequest;
use App\Http\Requests\UpdateSchoolClassRequest;
use App\Http\Resources\Admin\SchoolClassResource;
use App\SchoolClass;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SchoolClassesApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('school_class_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SchoolClassResource(SchoolClass::all());
    }

    public function store(StoreSchoolClassRequest $request)
    {
        $schoolClass = SchoolClass::create($request->all());

        return (new SchoolClassResource($schoolClass))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(SchoolClass $schoolClass)
    {
        abort_if(Gate::denies('school_class_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SchoolClassResource($schoolClass);
    }

    public function update(UpdateSchoolClassRequest $request, SchoolClass $schoolClass)
    {
        $schoolClass->update($request->all());

        return (new SchoolClassResource($schoolClass))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(SchoolClass $schoolClass)
    {
        abort_if(Gate::denies('school_class_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Check if there are students enrolled in this class
        $enrolledStudents = $schoolClass->classUsers()->where('is_student', true)->count();
        if ($enrolledStudents > 0) {
            return response()->json([
                'message' => "Cannot delete class '{$schoolClass->name}' because it has {$enrolledStudents} enrolled student(s). Please reassign or remove the students first."
            ], Response::HTTP_CONFLICT);
        }

        // Check if there are active lessons for this class
        $activeLessons = $schoolClass->lessons()->count();
        if ($activeLessons > 0) {
            return response()->json([
                'message' => "Cannot delete class '{$schoolClass->name}' because it has {$activeLessons} scheduled lesson(s). Please remove the lessons first."
            ], Response::HTTP_CONFLICT);
        }

        try {
            // Hard delete the class - completely remove from database
            $schoolClass->forceDelete();
            return response(null, Response::HTTP_NO_CONTENT);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return response()->json([
                    'message' => "Cannot delete class '{$schoolClass->name}' because it has associated data. Please remove all related records first."
                ], Response::HTTP_CONFLICT);
            }
            return response()->json([
                'message' => 'An error occurred while deleting the class. Please try again.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
