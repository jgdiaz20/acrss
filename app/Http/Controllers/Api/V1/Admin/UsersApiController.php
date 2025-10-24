<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new UserResource(User::with(['roles', 'class'])->get());
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->all());
        $user->roles()->sync($request->input('roles', []));

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(User $user)
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new UserResource($user->load(['roles', 'class']));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->all());
        $user->roles()->sync($request->input('roles', []));

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(User $user)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $userName = $user->name;
        
        // Check if teacher has assigned subjects
        if ($user->is_teacher && $user->teacherSubjects()->where('is_active', true)->exists()) {
            $assignedSubjects = $user->teacherSubjects()
                ->with('subject')
                ->where('is_active', true)
                ->get()
                ->pluck('subject.name')
                ->implode(', ');
            
            return response()->json([
                'message' => "Cannot delete teacher '{$userName}' because they are assigned to the following subjects: {$assignedSubjects}. Please remove the teacher from these subjects first."
            ], Response::HTTP_CONFLICT);
        }
        
        // Check if teacher has active lessons
        if ($user->is_teacher && $user->teacherLessons()->exists()) {
            return response()->json([
                'message' => "Cannot delete teacher '{$userName}' because they have scheduled lessons. Please delete or reassign all their lessons first."
            ], Response::HTTP_CONFLICT);
        }
        
        // Check if student is enrolled in a class
        if ($user->is_student && $user->class_id) {
            return response()->json([
                'message' => "Cannot delete student '{$userName}' because they are enrolled in a class. Please remove them from the class first."
            ], Response::HTTP_CONFLICT);
        }

        try {
            $user->forceDelete();

            return response(null, Response::HTTP_NO_CONTENT);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle foreign key constraint violations
            if ($e->getCode() == '23000') {
                return response()->json([
                    'message' => "Cannot delete user '{$userName}' because they have related data (lessons, subject assignments, etc.). Please remove all related data first."
                ], Response::HTTP_CONFLICT);
            }
            
            // Re-throw other database exceptions
            throw $e;
        }
    }
}
