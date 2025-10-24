<?php

namespace Tests\Feature;

use App\Lesson;
use App\Room;
use App\SchoolClass;
use App\Subject;
use App\User;
use App\Role;
use App\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchedulingFunctionalityTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $teacher;
    protected $schoolClass;
    protected $room;
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions
        $permissions = [
            'lesson_access', 'lesson_create', 'lesson_edit', 'lesson_show', 'lesson_delete',
            'room_access', 'room_create', 'room_edit', 'room_show', 'room_delete',
            'school_class_access', 'school_class_create', 'school_class_edit', 'school_class_show', 'school_class_delete',
            'subject_access', 'subject_create', 'subject_edit', 'subject_show', 'subject_delete'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['title' => $permission]);
        }

        // Create roles
        $adminRole = Role::create(['title' => 'Admin']);
        $teacherRole = Role::create(['title' => 'Teacher']);

        // Assign all permissions to admin
        $adminRole->permissions()->sync(Permission::all()->pluck('id'));

        // Assign lesson permissions to teacher - teachers can only VIEW their own timetable
        $teacherPermissions = Permission::whereIn('title', [
            'lesson_show', // Teachers can only view lessons, not create/edit/delete
            'room_show',   // Teachers can view room information
            'school_class_show', // Teachers can view class information
            'subject_show' // Teachers can view subject information
        ])->pluck('id');
        $teacherRole->permissions()->sync($teacherPermissions);

        // Create users
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'is_admin' => true
        ]);
        $this->admin->roles()->attach($adminRole->id);

        $this->teacher = User::create([
            'name' => 'Teacher User',
            'email' => 'teacher@test.com',
            'password' => bcrypt('password'),
            'is_teacher' => true
        ]);
        $this->teacher->roles()->attach($teacherRole->id);

        // Create test data
        $this->schoolClass = SchoolClass::create(['name' => 'Test Class']);
        $this->room = Room::create(['name' => 'Test Room', 'description' => 'Test Description']);
        $this->subject = Subject::create(['name' => 'Test Subject', 'code' => 'TS', 'type' => 'major']);
    }

    /** @test */
    public function admin_can_create_lesson_with_valid_data()
    {
        $lessonData = [
            'weekday' => 1,
            'start_time' => '10:00 AM',
            'end_time' => '11:00 AM',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/lessons', $lessonData);
        
        $response->assertRedirect('/admin/lessons');
        $this->assertDatabaseHas('lessons', [
            'weekday' => 1,
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ]);
    }

    /** @test */
    public function teacher_cannot_create_lessons()
    {
        $lessonData = [
            'weekday' => 2,
            'start_time' => '2:00 PM',
            'end_time' => '3:00 PM',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ];

        $response = $this->actingAs($this->teacher)->post('/admin/lessons', $lessonData);
        
        // Teachers should get 403 Forbidden when trying to create lessons
        $response->assertStatus(403);
        $this->assertDatabaseMissing('lessons', $lessonData);
    }

    /** @test */
    public function lesson_creation_requires_valid_weekday()
    {
        $lessonData = [
            'weekday' => 8, // Invalid weekday (should be 0-6)
            'start_time' => '10:00 AM',
            'end_time' => '11:00 AM',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/lessons', $lessonData);
        
        $response->assertSessionHasErrors('weekday');
        $this->assertDatabaseMissing('lessons', $lessonData);
    }

    /** @test */
    public function lesson_creation_requires_valid_time_format()
    {
        $lessonData = [
            'weekday' => 1,
            'start_time' => 'invalid_time',
            'end_time' => '11:00 AM',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/lessons', $lessonData);
        
        $response->assertSessionHasErrors('start_time');
        $this->assertDatabaseMissing('lessons', $lessonData);
    }

    /** @test */
    public function lesson_end_time_must_be_after_start_time()
    {
        $lessonData = [
            'weekday' => 1,
            'start_time' => '11:00 AM',
            'end_time' => '10:00 AM', // End time before start time
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/lessons', $lessonData);
        
        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('lessons', $lessonData);
    }

    /** @test */
    public function admin_can_update_existing_lesson()
    {
        $lesson = Lesson::create([
            'weekday' => 1,
            'start_time' => '10:00 AM',
            'end_time' => '11:00 AM',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ]);

        $updateData = [
            'weekday' => 2,
            'start_time' => '2:00 PM',
            'end_time' => '3:00 PM',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ];

        $response = $this->actingAs($this->admin)->put("/admin/lessons/{$lesson->id}", $updateData);
        
        $response->assertRedirect('/admin/lessons');
        $this->assertDatabaseHas('lessons', [
            'weekday' => 2,
            'start_time' => '14:00:00',
            'end_time' => '15:00:00',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
            'id' => $lesson->id
        ]);
    }

    /** @test */
    public function teacher_cannot_update_lessons()
    {
        $lesson = Lesson::create([
            'weekday' => 1,
            'start_time' => '10:00 AM',
            'end_time' => '11:00 AM',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ]);

        $updateData = [
            'weekday' => 2,
            'start_time' => '2:00 PM',
            'end_time' => '3:00 PM',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ];

        $response = $this->actingAs($this->teacher)->put("/admin/lessons/{$lesson->id}", $updateData);
        
        // Teachers should get 403 Forbidden when trying to update lessons
        $response->assertStatus(403);
        $this->assertDatabaseMissing('lessons', [
            'weekday' => 2,
            'start_time' => '14:00:00',
            'end_time' => '15:00:00',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
            'id' => $lesson->id
        ]);
    }

    /** @test */
    public function admin_can_delete_lesson()
    {
        $lesson = Lesson::create([
            'weekday' => 1,
            'start_time' => '10:00 AM',
            'end_time' => '11:00 AM',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/lessons/{$lesson->id}");
        
        $response->assertRedirect();
        $this->assertSoftDeleted('lessons', ['id' => $lesson->id]);
    }

    /** @test */
    public function teacher_cannot_delete_lessons()
    {
        $lesson = Lesson::create([
            'weekday' => 1,
            'start_time' => '10:00 AM',
            'end_time' => '11:00 AM',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ]);

        $response = $this->actingAs($this->teacher)->delete("/admin/lessons/{$lesson->id}");
        
        // Teachers should get 403 Forbidden when trying to delete lessons
        $response->assertStatus(403);
        $this->assertDatabaseHas('lessons', ['id' => $lesson->id]); // Lesson should still exist
    }

    /** @test */
    public function teacher_can_view_their_own_timetable()
    {
        // Create a lesson assigned to this teacher
        $lesson = Lesson::create([
            'weekday' => 1,
            'start_time' => '10:00 AM',
            'end_time' => '11:00 AM',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ]);

        // Teacher should be able to access their calendar/timetable
        $response = $this->actingAs($this->teacher)->get('/teacher/calendar');
        
        $response->assertSuccessful();
        $response->assertViewIs('teacher.calendar');
    }

    /** @test */
    public function lessons_are_displayed_in_correct_order()
    {
        // Create lessons with different weekdays and times
        $lesson1 = Lesson::create([
            'weekday' => 2,
            'start_time' => '2:00 PM',
            'end_time' => '3:00 PM',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ]);

        $lesson2 = Lesson::create([
            'weekday' => 1,
            'start_time' => '10:00 AM',
            'end_time' => '11:00 AM',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ]);

        $lesson3 = Lesson::create([
            'weekday' => 1,
            'start_time' => '12:00 PM',
            'end_time' => '1:00 PM',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/lessons');
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.lessons.index');
        
        // Verify lessons are ordered by weekday, then by start_time
        $lessons = $response->viewData('lessons');
        $this->assertEquals($lesson2->id, $lessons->first()->id); // Monday 10:00
        $this->assertEquals($lesson3->id, $lessons->skip(1)->first()->id); // Monday 12:00
        $this->assertEquals($lesson1->id, $lessons->skip(2)->first()->id); // Tuesday 14:00
    }

    /** @test */
    public function lesson_creation_requires_all_required_fields()
    {
        $incompleteData = [
            'weekday' => 1,
            'start_time' => '10:00 AM',
            'end_time' => '11:00 AM',
            // Missing teacher_id, class_id, room_id, subject_id
        ];

        $response = $this->actingAs($this->admin)->post('/admin/lessons', $incompleteData);
        
        $response->assertSessionHasErrors(['teacher_id', 'class_id', 'room_id', 'subject_id']);
        $this->assertDatabaseMissing('lessons', $incompleteData);
    }

    /** @test */
    public function lesson_creation_validates_teacher_exists()
    {
        $lessonData = [
            'weekday' => 1,
            'start_time' => '10:00 AM',
            'end_time' => '11:00 AM',
            'teacher_id' => 999, // Non-existent teacher
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/lessons', $lessonData);
        
        $response->assertSessionHasErrors('teacher_id');
        $this->assertDatabaseMissing('lessons', $lessonData);
    }

    /** @test */
    public function lesson_creation_validates_class_exists()
    {
        $lessonData = [
            'weekday' => 1,
            'start_time' => '10:00 AM',
            'end_time' => '11:00 AM',
            'teacher_id' => $this->teacher->id,
            'class_id' => 999, // Non-existent class
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/lessons', $lessonData);
        
        $response->assertSessionHasErrors('class_id');
        $this->assertDatabaseMissing('lessons', $lessonData);
    }

    /** @test */
    public function lesson_creation_validates_room_exists()
    {
        $lessonData = [
            'weekday' => 1,
            'start_time' => '10:00 AM',
            'end_time' => '11:00 AM',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => 999, // Non-existent room
            'subject_id' => $this->subject->id,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/lessons', $lessonData);
        
        $response->assertSessionHasErrors('room_id');
        $this->assertDatabaseMissing('lessons', $lessonData);
    }

    /** @test */
    public function lesson_creation_validates_subject_exists()
    {
        $lessonData = [
            'weekday' => 1,
            'start_time' => '10:00 AM',
            'end_time' => '11:00 AM',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => 999, // Non-existent subject
        ];

        $response = $this->actingAs($this->admin)->post('/admin/lessons', $lessonData);
        
        $response->assertSessionHasErrors('subject_id');
        $this->assertDatabaseMissing('lessons', $lessonData);
    }

    /** @test */
    public function lesson_creation_validates_school_hours_start_time()
    {
        $lessonData = [
            'weekday' => 1,
            'start_time' => '6:00 AM', // Before school hours (7 AM)
            'end_time' => '7:00 AM',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/lessons', $lessonData);
        
        $response->assertSessionHasErrors('start_time');
        $this->assertDatabaseMissing('lessons', $lessonData);
    }

    /** @test */
    public function lesson_creation_validates_school_hours_end_time()
    {
        $lessonData = [
            'weekday' => 1,
            'start_time' => '8:00 PM',
            'end_time' => '10:00 PM', // After school hours (9 PM)
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/lessons', $lessonData);
        
        $response->assertSessionHasErrors('end_time');
        $this->assertDatabaseMissing('lessons', $lessonData);
    }

    /** @test */
    public function lesson_creation_allows_valid_school_hours()
    {
        $lessonData = [
            'weekday' => 1,
            'start_time' => '8:00 AM', // Within school hours
            'end_time' => '9:00 AM',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/lessons', $lessonData);
        
        $response->assertRedirect('/admin/lessons');
        $this->assertDatabaseHas('lessons', [
            'weekday' => 1,
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ]);
    }

    /** @test */
    public function lesson_creation_allows_edge_school_hours()
    {
        $lessonData = [
            'weekday' => 1,
            'start_time' => '7:00 AM', // Exactly at school start time
            'end_time' => '9:00 PM',   // Exactly at school end time
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/lessons', $lessonData);
        
        $response->assertRedirect('/admin/lessons');
        $this->assertDatabaseHas('lessons', [
            'weekday' => 1,
            'start_time' => '07:00:00',
            'end_time' => '21:00:00',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ]);
    }
}
