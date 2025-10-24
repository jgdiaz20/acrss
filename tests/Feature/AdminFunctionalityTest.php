<?php

namespace Tests\Feature;

use App\Lesson;
use App\Permission;
use App\Role;
use App\Room;
use App\SchoolClass;
use App\Subject;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFunctionalityTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $teacher;
    protected $student;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions
        $permissions = [
            'user_access', 'user_create', 'user_edit', 'user_show', 'user_delete',
            'role_access', 'role_create', 'role_edit', 'role_show', 'role_delete',
            'permission_access', 'permission_create', 'permission_edit', 'permission_show', 'permission_delete',
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
        $studentRole = Role::create(['title' => 'Student']);

        // Assign all permissions to admin
        $adminRole->permissions()->sync(Permission::all()->pluck('id'));

        // Assign limited permissions to teacher
        $teacherPermissions = Permission::whereIn('title', [
            'lesson_access', 'lesson_create', 'lesson_edit', 'lesson_show',
            'room_access', 'room_show',
            'school_class_access', 'school_class_show',
            'subject_access', 'subject_show'
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

        $this->student = User::create([
            'name' => 'Student User',
            'email' => 'student@test.com',
            'password' => bcrypt('password'),
            'is_student' => true
        ]);
        $this->student->roles()->attach($studentRole->id);
    }

    /** @test */
    public function admin_can_access_admin_dashboard()
    {
        $response = $this->actingAs($this->admin)->get('/admin');
        
        $response->assertSuccessful();
        $response->assertViewIs('home');
    }

    /** @test */
    public function admin_can_access_users_management()
    {
        $response = $this->actingAs($this->admin)->get('/admin/users');
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.users.index');
    }

    /** @test */
    public function admin_can_access_roles_management()
    {
        $response = $this->actingAs($this->admin)->get('/admin/roles');
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.roles.index');
    }

    /** @test */
    public function admin_can_access_permissions_management()
    {
        $response = $this->actingAs($this->admin)->get('/admin/permissions');
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.permissions.index');
    }

    /** @test */
    public function admin_can_access_lessons_management()
    {
        $response = $this->actingAs($this->admin)->get('/admin/lessons');
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.lessons.index');
    }

    /** @test */
    public function admin_can_access_rooms_management()
    {
        $response = $this->actingAs($this->admin)->get('/admin/room-management/rooms');
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.rooms.index');
    }

    /** @test */
    public function admin_can_access_school_classes_management()
    {
        $response = $this->actingAs($this->admin)->get('/admin/school-classes');
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.school-classes.index');
    }

    /** @test */
    public function teacher_cannot_access_admin_users_management()
    {
        $response = $this->actingAs($this->teacher)->get('/admin/users');
        
        $response->assertStatus(403);
    }

    /** @test */
    public function teacher_cannot_access_admin_roles_management()
    {
        $response = $this->actingAs($this->teacher)->get('/admin/roles');
        
        $response->assertStatus(403);
    }

    /** @test */
    public function teacher_can_access_lessons_management()
    {
        $response = $this->actingAs($this->teacher)->get('/admin/lessons');
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.lessons.index');
    }

    /** @test */
    public function student_cannot_access_admin_panel()
    {
        $response = $this->actingAs($this->student)->get('/admin');
        
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_create_new_user()
    {
        $response = $this->actingAs($this->admin)->get('/admin/users/create');
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.users.create');
    }

    /** @test */
    public function admin_can_create_new_lesson()
    {
        // Create required dependencies
        $schoolClass = SchoolClass::create(['name' => 'Test Class']);
        $room = Room::create(['name' => 'Test Room', 'description' => 'Test Description']);
        $subject = Subject::create(['name' => 'Test Subject', 'code' => 'TS', 'type' => 'major']);

        $response = $this->actingAs($this->admin)->get('/admin/lessons/create');
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.lessons.create');
    }

    /** @test */
    public function admin_can_create_new_room()
    {
        $response = $this->actingAs($this->admin)->get('/admin/room-management/rooms/create');
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.rooms.create');
    }

    /** @test */
    public function admin_can_create_new_school_class()
    {
        $response = $this->actingAs($this->admin)->get('/admin/school-classes/create');
        
        if ($response->status() !== 200) {
            dump('Status: ' . $response->status());
            dump('Content: ' . substr($response->content(), 0, 1000));
        }
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.school-classes.create');
    }

    /** @test */
    public function admin_can_create_new_role()
    {
        $response = $this->actingAs($this->admin)->get('/admin/roles/create');
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.roles.create');
    }

    /** @test */
    public function admin_can_create_new_permission()
    {
        $response = $this->actingAs($this->admin)->get('/admin/permissions/create');
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.permissions.create');
    }

    /** @test */
    public function admin_can_create_new_subject()
    {
        $response = $this->actingAs($this->admin)->get('/admin/subjects/create');
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.subjects.create');
    }

    /** @test */
    public function admin_can_access_room_timetables()
    {
        $response = $this->actingAs($this->admin)->get('/admin/room-management/room-timetables');
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.room-timetable.index');
    }

    /** @test */
    public function teacher_cannot_create_users()
    {
        $response = $this->actingAs($this->teacher)->get('/admin/users/create');
        
        $response->assertStatus(403);
    }

    /** @test */
    public function teacher_cannot_create_roles()
    {
        $response = $this->actingAs($this->teacher)->get('/admin/roles/create');
        
        $response->assertStatus(403);
    }

    /** @test */
    public function teacher_cannot_create_permissions()
    {
        $response = $this->actingAs($this->teacher)->get('/admin/permissions/create');
        
        $response->assertStatus(403);
    }

    /** @test */
    public function teacher_can_create_lessons()
    {
        // Create required dependencies
        $schoolClass = SchoolClass::create(['name' => 'Test Class']);
        $room = Room::create(['name' => 'Test Room', 'description' => 'Test Description']);
        $subject = Subject::create(['name' => 'Test Subject', 'code' => 'TS', 'type' => 'major']);

        $response = $this->actingAs($this->teacher)->get('/admin/lessons/create');
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.lessons.create');
    }

    /** @test */
    public function unauthorized_user_cannot_access_admin_panel()
    {
        $response = $this->get('/admin');
        
        $response->assertRedirect('/login');
    }

    /** @test */
    public function admin_can_view_lesson_details()
    {
        // Create required dependencies
        $schoolClass = SchoolClass::create(['name' => 'Test Class']);
        $room = Room::create(['name' => 'Test Room', 'description' => 'Test Description']);
        $subject = Subject::create(['name' => 'Test Subject', 'code' => 'TS', 'type' => 'major']);
        
        $lesson = Lesson::create([
            'weekday' => 1,
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'teacher_id' => $this->teacher->id,
            'class_id' => $schoolClass->id,
            'room_id' => $room->id,
            'subject_id' => $subject->id,
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/lessons/{$lesson->id}");
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.lessons.show');
    }

    /** @test */
    public function admin_can_view_room_details()
    {
        $room = Room::create(['name' => 'Test Room', 'description' => 'Test Description']);

        $response = $this->actingAs($this->admin)->get("/admin/room-management/rooms/{$room->id}");
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.rooms.show');
    }

    /** @test */
    public function admin_can_view_school_class_details()
    {
        $schoolClass = SchoolClass::create(['name' => 'Test Class']);

        $response = $this->actingAs($this->admin)->get("/admin/school-classes/{$schoolClass->id}");
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.school-classes.show');
    }

    /** @test */
    public function admin_can_view_role_details()
    {
        $role = Role::create(['title' => 'Test Role']);

        $response = $this->actingAs($this->admin)->get("/admin/roles/{$role->id}");
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.roles.show');
    }

    /** @test */
    public function admin_can_view_permission_details()
    {
        $permission = Permission::create(['title' => 'test_permission']);

        $response = $this->actingAs($this->admin)->get("/admin/permissions/{$permission->id}");
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.permissions.show');
    }

    /** @test */
    public function admin_can_view_user_details()
    {
        $response = $this->actingAs($this->admin)->get("/admin/users/{$this->teacher->id}");
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.users.show');
    }

    /** @test */
    public function admin_can_view_subject_details()
    {
        $subject = Subject::create(['name' => 'Test Subject', 'code' => 'TS', 'type' => 'major']);

        $response = $this->actingAs($this->admin)->get("/admin/subjects/{$subject->id}");
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.subjects.show');
    }

    /** @test */
    public function admin_can_mass_delete_subjects_without_teachers_or_lessons()
    {
        // Create subjects without any teachers or lessons assigned
        $subject1 = Subject::create(['name' => 'Test Subject 1', 'code' => 'TS1', 'type' => 'major']);
        $subject2 = Subject::create(['name' => 'Test Subject 2', 'code' => 'TS2', 'type' => 'minor']);
        
        // Verify subjects exist
        $this->assertDatabaseHas('subjects', ['id' => $subject1->id]);
        $this->assertDatabaseHas('subjects', ['id' => $subject2->id]);
        
        // Make mass delete request
        $response = $this->actingAs($this->admin)->delete('/admin/subjects/destroy', [
            'ids' => [$subject1->id, $subject2->id]
        ]);
        
        // Should return successful response
        $response->assertStatus(200);
        $response->assertJson([
            'message' => '2 subjects have been successfully deleted!'
        ]);
        
        // Verify subjects are deleted
        $this->assertDatabaseMissing('subjects', ['id' => $subject1->id]);
        $this->assertDatabaseMissing('subjects', ['id' => $subject2->id]);
    }

    /** @test */
    public function admin_cannot_mass_delete_subjects_with_teachers_assigned()
    {
        // Create subjects
        $subject1 = Subject::create(['name' => 'Test Subject 1', 'code' => 'TS1', 'type' => 'major']);
        $subject2 = Subject::create(['name' => 'Test Subject 2', 'code' => 'TS2', 'type' => 'minor']);
        
        // Assign teacher to subject1
        $subject1->teachers()->attach($this->teacher->id, [
            'is_primary' => true,
            'experience_years' => 5,
            'is_active' => true
        ]);
        
        // Make mass delete request
        $response = $this->actingAs($this->admin)->delete('/admin/subjects/destroy', [
            'ids' => [$subject1->id, $subject2->id]
        ]);
        
        // Should return partial content (some deleted, some not)
        $response->assertStatus(206);
        
        // Verify subject1 still exists (has teacher), subject2 is deleted
        $this->assertDatabaseHas('subjects', ['id' => $subject1->id]);
        $this->assertDatabaseMissing('subjects', ['id' => $subject2->id]);
    }
}
