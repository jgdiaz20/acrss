<?php

namespace Tests\Feature;

use App\Lesson;
use App\Role;
use App\SchoolClass;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Calendar Page Returns 200 code, so no errors.
     *
     * @return void
     */
    public function testCalendarPageIsLoadingForAdmin()
    {
        $role = Role::create([
            'title' => 'Admin'
        ]);
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'is_admin' => true
        ]);
        $admin->roles()->attach($role->id);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertSuccessful();
    }

    /**
     * Test Calendar Page Shows the Lesson we created
     *
     * @return void
     */
    public function testCalendarPageShowsLessonForTeacher()
    {
        // Create class
        $schoolClass = SchoolClass::create([
            'name' => 'Class no.1'
        ]);

        // Create teacher
        $role = Role::create([
            'title' => 'Teacher'
        ]);
        
        // Give teacher lesson access permission
        $lessonPermission = \App\Permission::create([
            'title' => 'lesson_access'
        ]);
        $role->permissions()->attach($lessonPermission->id);
        
        $teacher = User::create([
            'name' => 'Teacher',
            'email' => 'teacher@teacher.com',
            'password' => bcrypt('password'),
            'is_teacher' => true
        ]);
        $teacher->roles()->attach($role->id);

        // Create room
        $room = \App\Room::create([
            'name' => 'Room 101',
            'description' => 'Test Room'
        ]);

        // Create lesson
        Lesson::create([
            'weekday' => 1,
            'start_time' => '10:00 AM',
            'end_time' => '12:00 PM',
            'teacher_id' => $teacher->id,
            'class_id' => $schoolClass->id,
            'room_id' => $room->id,
        ]);

        $response = $this->actingAs($teacher)->get('/admin/lessons');

        $response->assertSuccessful();
        $response->assertSeeText($schoolClass->name);
    }
}
