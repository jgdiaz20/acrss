<?php

namespace App\Http\Controllers\Admin;

use App\Room;
use App\Lesson;
use App\User;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class HomeController
{
    public function index()
    {
        $user = auth()->user();
        
        // Check if user is authenticated
        if (!$user) {
            abort(401, 'Authentication required.');
        }
        
        // Check if user is admin
        if (!$user->is_admin) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $rooms = Room::take(5)->get();
        $totalRooms = Room::count();
        $totalLessons = Lesson::count();
        $activeTeachers = User::where('is_teacher', true)->count();
        
        return view('home', compact('rooms', 'totalRooms', 'totalLessons', 'activeTeachers'));
    }
}
