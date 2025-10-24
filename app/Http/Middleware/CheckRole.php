<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();
        
        // Additional null check for extra safety
        if (!$user) {
            return redirect('/login')->with('error', 'Authentication failed. Please log in again.');
        }
        
        switch ($role) {
            case 'teacher':
                if (!$user->is_teacher) {
                    abort(403, 'Access denied. Teacher role required.');
                }
                break;
            case 'admin':
                if (!$user->is_admin) {
                    abort(403, 'Access denied. Admin role required.');
                }
                break;
            case 'student':
                if (!$user->is_student) {
                    abort(403, 'Access denied. Student role required.');
                }
                break;
            default:
                abort(403, 'Invalid role specified.');
        }

        return $next($request);
    }
}
