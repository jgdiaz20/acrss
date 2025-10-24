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

class InlineEditingTest extends TestCase
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
    public function admin_can_get_lesson_form_data_for_inline_editing()
    {
        $response = $this->actingAs($this->admin)->get('/admin/lessons/form-data');
        
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'classes',
            'teachers',
            'rooms',
            'subjects'
        ]);
    }

    /** @test */
    public function teacher_cannot_get_lesson_form_data_for_inline_editing()
    {
        $response = $this->actingAs($this->teacher)->get('/admin/lessons/form-data');
        
        // Teachers should not have access to form data for creating lessons
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_get_lesson_details_for_inline_editing()
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

        $response = $this->actingAs($this->admin)->get("/admin/lessons/{$lesson->id}/details");
        
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'id',
            'weekday',
            'start_time',
            'end_time',
            'teacher_id',
            'class_id',
            'room_id',
            'subject_id'
        ]);
    }

    /** @test */
    public function teacher_cannot_get_lesson_details_for_inline_editing()
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

        $response = $this->actingAs($this->teacher)->get("/admin/lessons/{$lesson->id}/details");
        
        // Teachers should not have access to lesson details for inline editing
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_create_lesson_via_inline_editing()
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

        $response = $this->actingAs($this->admin)->post('/admin/lessons/inline', $lessonData);
        
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'success',
            'message',
            'lesson'
        ]);
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
    public function teacher_cannot_create_lesson_via_inline_editing()
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

        $response = $this->actingAs($this->teacher)->post('/admin/lessons/inline', $lessonData);
        
        // Teachers should get 403 Forbidden when trying to create lessons
        $response->assertStatus(403);
        $this->assertDatabaseMissing('lessons', $lessonData);
    }

    /** @test */
    public function admin_can_update_lesson_via_inline_editing()
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

        $response = $this->actingAs($this->admin)->put("/admin/lessons/{$lesson->id}/inline", $updateData);
        
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'success',
            'message',
            'lesson'
        ]);
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
    public function teacher_cannot_update_lesson_via_inline_editing()
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

        $response = $this->actingAs($this->teacher)->put("/admin/lessons/{$lesson->id}/inline", $updateData);
        
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
    public function admin_can_delete_lesson_via_inline_editing()
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

        $response = $this->actingAs($this->admin)->delete("/admin/lessons/{$lesson->id}/inline");
        
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'success',
            'message'
        ]);
        $this->assertSoftDeleted('lessons', ['id' => $lesson->id]);
    }

    /** @test */
    public function teacher_cannot_delete_lesson_via_inline_editing()
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

        $response = $this->actingAs($this->teacher)->delete("/admin/lessons/{$lesson->id}/inline");
        
        // Teachers should get 403 Forbidden when trying to delete lessons
        $response->assertStatus(403);
        $this->assertDatabaseHas('lessons', ['id' => $lesson->id]); // Lesson should still exist
    }

    /** @test */
    public function admin_can_check_lesson_conflicts()
    {
        // Create an existing lesson
        Lesson::create([
            'weekday' => 1,
            'start_time' => '10:00 AM',
            'end_time' => '11:00 AM',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ]);

        $conflictData = [
            'weekday' => 1,
            'start_time' => '10:30 AM',
            'end_time' => '11:30 AM',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/lessons/check-conflicts', $conflictData);
        
        $response->assertSuccessful();
        
        $response->assertJsonStructure([
            'conflicts'
        ]);
    }

    /** @test */
    public function teacher_cannot_check_lesson_conflicts()
    {
        // Create an existing lesson
        Lesson::create([
            'weekday' => 1,
            'start_time' => '10:00 AM',
            'end_time' => '11:00 AM',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ]);

        $conflictData = [
            'weekday' => 1,
            'start_time' => '10:30 AM',
            'end_time' => '11:30 AM',
            'teacher_id' => $this->teacher->id,
            'class_id' => $this->schoolClass->id,
            'room_id' => $this->room->id,
            'subject_id' => $this->subject->id,
        ];

        $response = $this->actingAs($this->teacher)->post('/admin/lessons/check-conflicts', $conflictData);
        
        // Teachers should not be able to check conflicts as they can't edit lessons
        $response->assertStatus(403);
    }

    /** @test */
    public function inline_lesson_creation_validates_required_fields()
    {
        $incompleteData = [
            'weekday' => 1,
            'start_time' => '10:00 AM',
            'end_time' => '11:00 AM',
            // Missing teacher_id, class_id, room_id, subject_id
        ];

        $response = $this->actingAs($this->admin)->post('/admin/lessons/inline', $incompleteData);
        
        $response->assertStatus(422);
        
        $response->assertJsonStructure([
            'error',
            'messages'
        ]);
        $this->assertDatabaseMissing('lessons', $incompleteData);
    }

    /** @test */
    public function inline_lesson_update_validates_required_fields()
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

        $incompleteData = [
            'weekday' => 1,
            'start_time' => '10:00 AM',
            'end_time' => '11:00 AM',
            // Missing teacher_id, class_id, room_id, subject_id
        ];

        $response = $this->actingAs($this->admin)->put("/admin/lessons/{$lesson->id}/inline", $incompleteData);
        
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'error',
            'messages'
        ]);
    }

    /** @test */
    public function unauthorized_user_cannot_access_inline_lesson_endpoints()
    {
        $response = $this->get('/admin/lessons/form-data');
        $response->assertRedirect('/login');

        $response = $this->post('/admin/lessons/inline', []);
        $response->assertRedirect('/login');

        $response = $this->put('/admin/lessons/1/inline', []);
        $response->assertRedirect('/login');

        $response = $this->delete('/admin/lessons/1/inline');
        $response->assertRedirect('/login');

        $response = $this->post('/admin/lessons/check-conflicts', []);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function inline_lesson_form_data_includes_all_required_options()
    {
        $response = $this->actingAs($this->admin)->get('/admin/lessons/form-data');
        
        $response->assertSuccessful();
        $data = $response->json();
        
        $this->assertArrayHasKey('classes', $data);
        $this->assertArrayHasKey('teachers', $data);
        $this->assertArrayHasKey('rooms', $data);
        $this->assertArrayHasKey('subjects', $data);
        
        
        // Verify the data includes our test data
        $classNames = collect($data['classes'])->pluck('name')->toArray();
        $teacherNames = collect($data['teachers'])->pluck('name')->toArray();
        $roomNames = collect($data['rooms'])->pluck('name')->toArray();
        $subjectNames = collect($data['subjects'])->pluck('name')->toArray();
        
        $this->assertContains($this->schoolClass->name, $classNames);
        $this->assertContains($this->teacher->name, $teacherNames);
        $this->assertContains($this->room->name, $roomNames);
        $this->assertContains($this->subject->name, $subjectNames);
    }
}
