<?php

Route::redirect('/', '/login');
Route::get('/home', function () {
    $user = auth()->user();

    // Check if user is authenticated
    if (!$user) {
        return redirect()->route('login')->with('error', 'Please log in to access this page.');
    }

    // Admin takes priority, then teacher
    if ($user->is_admin) {
        $routeName = 'admin.home';
    } elseif ($user->is_teacher) {
        $routeName = 'teacher.dashboard';
    } else {
        // No valid role assigned
        abort(403, 'Access denied. No valid role assigned.');
    }
    
    if (session('status')) {
        return redirect()->route($routeName)->with('status', session('status'));
    }

    return redirect()->route($routeName);
});

// Authentication routes (register, reset, verify, confirm disabled)
Auth::routes([
    'register' => false,
    'reset' => false,      // Disable password reset
    'verify' => false,     // Disable email verification
    'confirm' => false     // Disable password confirmation
]);

// Override login route with rate limiting (5 attempts per minute)
Route::post('login', 'Auth\LoginController@login')
    ->middleware('throttle:5,1')
    ->name('login');

// Override logout route to handle expired sessions gracefully
Route::post('logout', 'Auth\LoginController@logout')
    ->name('logout')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Admin

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth', 'App\Http\Middleware\AuthGates']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    
    // Validation API endpoints for enhanced modal
    Route::post('validation/check-conflicts', 'ValidationController@checkConflicts')->name('validation.checkConflicts');
    Route::post('validation/available-rooms', 'ValidationController@getAvailableRooms')->name('validation.availableRooms');
    Route::get('validation/subjects/{subject}/teachers', 'ValidationController@getTeachersForSubject')->name('validation.teachersForSubject');
    Route::post('validation/teacher-availability', 'ValidationController@checkTeacherAvailability')->name('validation.teacherAvailability');
    Route::post('validation/alternative-times', 'ValidationController@getAlternativeTimeSlots')->name('validation.alternativeTimes');
    
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Lesson Inline Editing (must be before resource routes to avoid conflicts)
    Route::get('lessons/form-data', 'LessonInlineController@getFormData')->name('lessons.form-data');
    Route::get('lessons/{id}/details', 'LessonInlineController@getLesson')->name('lessons.details');
    Route::post('lessons/inline', 'LessonInlineController@store')->name('lessons.inline.store');
    Route::put('lessons/{id}/inline', 'LessonInlineController@update')->name('lessons.inline.update');
    Route::delete('lessons/{id}/inline', 'LessonInlineController@destroy')->name('lessons.inline.destroy');
    Route::post('lessons/check-conflicts', 'LessonInlineController@checkConflicts')->name('lessons.check-conflicts');
    
    // Lessons
    Route::delete('lessons/destroy', 'LessonsController@massDestroy')->name('lessons.massDestroy');
    Route::get('lessons/get-teachers-for-subject', 'LessonsController@getTeachersForSubject')->name('lessons.get-teachers-for-subject');
    Route::get('lessons/get-rooms-for-subject', 'LessonsController@getRoomsForSubject')->name('lessons.get-rooms-for-subject');
    Route::get('lessons/hours-tracking', 'LessonsController@getHoursTracking')->name('lessons.hours-tracking');
    Route::get('lessons/{lesson}/info', 'LessonsController@getInfo')->name('lessons.info');
    Route::resource('lessons', 'LessonsController');

    // School Classes
    Route::delete('school-classes/destroy', 'SchoolClassesController@massDestroy')->name('school-classes.massDestroy');
    Route::get('school-classes/program/{program}', 'SchoolClassesController@byProgram')->name('school-classes.program');
    Route::get('school-classes/program/{program}/grade/{gradeLevel}', 'SchoolClassesController@byProgramAndGrade')->name('school-classes.program.grade');
    Route::get('school-classes/manage/{programId}', 'SchoolClassesController@manageProgram')->name('school-classes.manage');
    Route::get('school-classes/{schoolClass}/program-type', 'SchoolClassesController@getProgramType')->name('school-classes.program-type');
    Route::resource('school-classes', 'SchoolClassesController');

     // Grade Levels (for dropdowns)
    Route::get('grade-levels/by-program/{programId}', 'GradeLevelController@byProgram')->name('admin.grade-levels.by-program');

    // Room Management Group
    Route::prefix('room-management')->group(function () {
        // Rooms
        Route::delete('rooms/destroy', 'RoomsController@massDestroy')->name('room-management.rooms.massDestroy');
        Route::get('rooms/{room}/qr-code', 'RoomsController@showQRCode')->name('room-management.rooms.qr-code');
        Route::get('rooms/qr-codes/all', 'RoomsController@generateAllQRCodes')->name('room-management.rooms.qr-codes.all');
        Route::resource('rooms', 'RoomsController')->names([
            'index' => 'room-management.rooms.index',
            'create' => 'room-management.rooms.create',
            'store' => 'room-management.rooms.store',
            'show' => 'room-management.rooms.show',
            'edit' => 'room-management.rooms.edit',
            'update' => 'room-management.rooms.update',
            'destroy' => 'room-management.rooms.destroy'
        ]);

        // Room Timetables
        Route::get('room-timetables', 'RoomTimetableController@index')->name('room-management.room-timetables.index');
        Route::get('room-timetables/{room}', 'RoomTimetableController@show')->name('room-management.room-timetables.show');
        Route::get('room-timetables/{room}/qr-code', 'RoomTimetableController@showQRCode')->name('room-management.room-timetables.qr-code');
        Route::get('room-timetables/qr-codes/all', 'RoomTimetableController@generateAllQRCodes')->name('room-management.room-timetables.qr-codes.all');

        // Master Timetable
        Route::prefix('master-timetable')->group(function () {
            Route::get('/', 'MasterTimetableController@index')->name('room-management.master-timetable.index');
            
            // AJAX endpoints - must be defined BEFORE the {weekday} route
            Route::get('/available-slots', 'MasterTimetableController@getAvailableTimeSlots')->name('room-management.master-timetable.available-slots');
            Route::get('/room-utilization', 'MasterTimetableController@getRoomUtilization')->name('room-management.master-timetable.room-utilization');
            Route::get('/timetable-data', 'MasterTimetableController@getTimetableData')->name('room-management.master-timetable.timetable-data');
            Route::get('/lesson-details', 'MasterTimetableController@getLessonDetails')->name('room-management.master-timetable.lesson-details');
            Route::post('/check-conflicts', 'MasterTimetableController@checkSchedulingConflicts')->name('room-management.master-timetable.check-conflicts');
            Route::get('/quick-stats', 'MasterTimetableController@getQuickStats')->name('room-management.master-timetable.quick-stats');
            Route::get('/export', 'MasterTimetableController@export')->name('room-management.master-timetable.export');
            Route::get('/export-all', 'MasterTimetableController@exportAll')->name('room-management.master-timetable.export-all');
            
            // This route must be LAST to avoid conflicts with specific routes above
            Route::get('/{weekday}', 'MasterTimetableController@show')->name('room-management.master-timetable.show');
        });
    });

    // Academic Programs
    Route::delete('academic-programs/destroy', 'AcademicProgramController@massDestroy')->name('academic-programs.massDestroy');
    Route::resource('academic-programs', 'AcademicProgramController');


    // Subjects
    Route::delete('subjects/destroy', 'SubjectsController@massDestroy')->name('subjects.massDestroy');
    Route::get('subjects/{subject}/assign-teachers', 'SubjectsController@assignTeachers')->name('subjects.assign-teachers');
    Route::post('subjects/{subject}/assign-teachers', 'SubjectsController@updateTeacherAssignments')->name('subjects.update-teachers');
    Route::resource('subjects', 'SubjectsController');


});

// Teacher Routes
Route::group(['prefix' => 'teacher', 'as' => 'teacher.', 'middleware' => ['auth', 'role:teacher']], function () {
    Route::get('/', 'TeacherDashboardController@index')->name('dashboard');
    Route::get('calendar', 'TeacherCalendarController@index')->name('calendar.index');
});

// Public Routes (No Authentication Required)
Route::get('/public/room/{identifier}', 'PublicRoomController@show')->name('public.room.timetable');
