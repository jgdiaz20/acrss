<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Role;
use App\SchoolClass;
use App\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $perPage = $request->get('per_page', 20);
        $users = User::when($request->role, function ($query) use ($request) {
                $query->whereHas('roles', function ($query) use ($request) {
                    $query->whereId($request->role);
                });
            })
            ->paginate($perPage);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Only show Admin and Teacher roles (Student role handled by separate "Add New Student" button)
        $roles = Role::whereIn('id', [1, 3])->pluck('title', 'id');

        $classes = SchoolClass::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.users.create', compact('roles', 'classes'));
    }

    public function createStudent()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Only show Student role for student creation
        $roles = Role::whereIn('id', [4])->pluck('title', 'id');

        $classes = SchoolClass::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.users.create-student', compact('roles', 'classes'));
    }

    public function store(StoreUserRequest $request)
    {
        $userData = $request->all();
        
        // Set role-specific flags based on selected roles
        $roles = $request->input('roles', []);
        $userData['is_admin'] = in_array(1, $roles);
        $userData['is_teacher'] = in_array(3, $roles);
        $userData['is_student'] = in_array(4, $roles);
        
        $user = User::create($userData);
        $user->roles()->sync($roles);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

    public function edit(User $user)
    {
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Show appropriate roles based on user type
        if ($user->is_student) {
            // For students, only show student role (locked)
            $roles = Role::whereIn('id', [4])->pluck('title', 'id');
        } else {
            // For admins and teachers, show admin and teacher roles
            $roles = Role::whereIn('id', [1, 3])->pluck('title', 'id');
        }

        $classes = SchoolClass::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $user->load('roles', 'class');

        // Add current password hash for JavaScript validation
        $currentPasswordHash = $user->password;

        return view('admin.users.edit', compact('roles', 'classes', 'user', 'currentPasswordHash'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $userData = $request->all();
        
        // Only update password if provided (not empty)
        if (empty($userData['password'])) {
            unset($userData['password']);
        } else {
            // Hash the password before saving
            $userData['password'] = bcrypt($userData['password']);
        }
        
        // Set role-specific flags based on selected roles
        $roles = $request->input('roles', []);
        $userData['is_admin'] = in_array(1, $roles);
        $userData['is_teacher'] = in_array(3, $roles);
        $userData['is_student'] = in_array(4, $roles);
        
        $user->update($userData);
        $user->roles()->sync($roles);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    public function show(User $user)
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->load('roles', 'class', 'teacherLessons');

        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $userName = $user->name; // Store name before deletion for message
        
        // Check if teacher has assigned subjects
        if ($user->is_teacher && $user->teacherSubjects()->where('is_active', true)->exists()) {
            $assignedSubjects = $user->teacherSubjects()
                ->with('subject')
                ->where('is_active', true)
                ->get()
                ->pluck('subject.name')
                ->implode(', ');
            
            return back()->with('error', "Cannot delete teacher '{$userName}' because they are assigned to the following subjects: {$assignedSubjects}. Please remove the teacher from these subjects first.");
        }
        
        // Check if teacher has active lessons
        if ($user->is_teacher && $user->teacherLessons()->exists()) {
            return back()->with('error', "Cannot delete teacher '{$userName}' because they have scheduled lessons. Please delete or reassign all their lessons first.");
        }
        
        // Check if student is enrolled in a class
        if ($user->is_student && $user->class_id) {
            return back()->with('error', "Cannot delete student '{$userName}' because they are enrolled in a class. Please remove them from the class first.");
        }

        try {
            // Hard delete the user - completely remove from database
            $user->forceDelete();

            return back()->with('success', "User '{$userName}' has been successfully deleted!");
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle foreign key constraint violations
            if ($e->getCode() == '23000') {
                return back()->with('error', "Cannot delete user '{$userName}' because they have related data (lessons, subject assignments, etc.). Please remove all related data first.");
            }
            
            // Re-throw other database exceptions
            throw $e;
        }
    }

    public function massDestroy(MassDestroyUserRequest $request)
    {
        $userIds = request('ids');
        $users = User::whereIn('id', $userIds)->get();
        $deletedUsers = [];
        $errors = [];

        foreach ($users as $user) {
            $userName = $user->name;
            
            // Check if teacher has assigned subjects
            if ($user->is_teacher && $user->teacherSubjects()->where('is_active', true)->exists()) {
                $assignedSubjects = $user->teacherSubjects()
                    ->with('subject')
                    ->where('is_active', true)
                    ->get()
                    ->pluck('subject.name')
                    ->implode(', ');
                
                $errors[] = "Cannot delete teacher '{$userName}' because they are assigned to subjects: {$assignedSubjects}.";
                continue;
            }
            
            // Check if teacher has active lessons
            if ($user->is_teacher && $user->teacherLessons()->exists()) {
                $errors[] = "Cannot delete teacher '{$userName}' because they have scheduled lessons.";
                continue;
            }
            
            // Check if student is enrolled in a class
            if ($user->is_student && $user->class_id) {
                $errors[] = "Cannot delete student '{$userName}' because they are enrolled in a class.";
                continue;
            }

            try {
                $user->forceDelete();
                $deletedUsers[] = $userName;
            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() == '23000') {
                    $errors[] = "Cannot delete user '{$userName}' because they have related data.";
                } else {
                    throw $e;
                }
            }
        }

        $deletedCount = count($deletedUsers);
        $errorCount = count($errors);
        
        if ($deletedCount === 0) {
            return response()->json([
                'message' => 'No users were deleted.',
                'errors' => $errors
            ], Response::HTTP_BAD_REQUEST);
        } elseif ($errorCount > 0) {
            return response()->json([
                'message' => "{$deletedCount} users deleted successfully, {$errorCount} users could not be deleted.",
                'errors' => $errors
            ], Response::HTTP_PARTIAL_CONTENT);
        } else {
            if ($deletedCount === 1) {
                return response()->json(['message' => "User '{$deletedUsers[0]}' has been successfully deleted!"], Response::HTTP_OK);
            } else {
                return response()->json(['message' => "{$deletedCount} users have been successfully deleted!"], Response::HTTP_OK);
            }
        }
    }
}
