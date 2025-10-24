<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Room;
use App\SchoolClass;
use App\Subject;
use App\Lesson;
use App\AcademicProgram;
use App\GradeLevel;
use App\TeacherSubject;

class MasterTimetableTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $teacher;
    protected $room1;
    protected $room2;
    protected $class1;
    protected $class2;
    protected $subject1;
    protected $subject2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'is_admin' => true,
            'is_teacher' => false,
            'is_student' => false
        ]);

        // Create teacher user
        $this->teacher = User::factory()->create([
            'is_admin' => false,
            'is_teacher' => true,
            'is_student' => false
        ]);

        // Create academic program and grade level
        $program = AcademicProgram::factory()->create([
            'name' => 'Computer Science',
            'code' => 'CS'
        ]);

        $gradeLevel = GradeLevel::factory()->create([
            'level_name' => 'Grade 10',
            'program_id' => $program->id
        ]);

        // Create school classes
        $this->class1 = SchoolClass::factory()->create([
            'name' => 'Grade 10-A',
            'program_id' => $program->id,
            'grade_level_id' => $gradeLevel->id,
            'section' => 'A'
        ]);

        $this->class2 = SchoolClass::factory()->create([
            'name' => 'Grade 10-B',
            'program_id' => $program->id,
            'grade_level_id' => $gradeLevel->id,
            'section' => 'B'
        ]);

        // Create rooms
        $this->room1 = Room::factory()->create([
            'name' => 'Computer Lab 1',
            'capacity' => 30,
            'is_lab' => true,
            'has_equipment' => true
        ]);

        $this->room2 = Room::factory()->create([
            'name' => 'Classroom 101',
            'capacity' => 25,
            'is_lab' => false,
            'has_equipment' => false
        ]);

        // Create subjects
        $this->subject1 = Subject::factory()->create([
            'name' => 'Programming',
            'code' => 'PROG101',
            'requires_lab' => true,
            'requires_equipment' => true
        ]);

        $this->subject2 = Subject::factory()->create([
            'name' => 'Mathematics',
            'code' => 'MATH101',
            'requires_lab' => false,
            'requires_equipment' => false
        ]);

        // Create teacher-subject assignments
        TeacherSubject::factory()->create([
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subject1->id,
            'is_active' => true,
            'is_primary' => true
        ]);

        TeacherSubject::factory()->create([
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subject2->id,
            'is_active' => true,
            'is_primary' => false
        ]);
    }

    /** @test */
    public function admin_can_access_master_timetable_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.room-management.master-timetable.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.room-management.master-timetable.index');
        $response->assertSee('Master Timetable Overview');
    }

    /** @test */
    public function non_admin_cannot_access_master_timetable()
    {
        $response = $this->actingAs($this->teacher)
            ->get(route('admin.room-management.master-timetable.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_view_master_timetable_for_specific_day()
    {
        // Create some test lessons
        Lesson::factory()->create([
            'weekday' => 1, // Monday
            'class_id' => $this->class1->id,
            'teacher_id' => $this->teacher->id,
            'room_id' => $this->room1->id,
            'subject_id' => $this->subject1->id,
            'start_time' => '08:00:00',
            'end_time' => '09:30:00'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.room-management.master-timetable.show', 1));

        $response->assertStatus(200);
        $response->assertViewIs('admin.room-management.master-timetable.show');
        $response->assertSee('Master Timetable - Monday');
    }

    /** @test */
    public function master_timetable_displays_correct_lesson_information()
    {
        // Create a lesson for Monday
        $lesson = Lesson::factory()->create([
            'weekday' => 1, // Monday
            'class_id' => $this->class1->id,
            'teacher_id' => $this->teacher->id,
            'room_id' => $this->room1->id,
            'subject_id' => $this->subject1->id,
            'start_time' => '08:00:00',
            'end_time' => '09:30:00'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.room-management.master-timetable.show', 1));

        $response->assertStatus(200);
        $response->assertSee($lesson->subject->name);
        $response->assertSee($lesson->class->name);
        $response->assertSee($lesson->teacher->name);
    }

    /** @test */
    public function master_timetable_shows_empty_slots_correctly()
    {
        // Don't create any lessons for Monday
        $response = $this->actingAs($this->admin)
            ->get(route('admin.room-management.master-timetable.show', 1));

        $response->assertStatus(200);
        $response->assertSee('Available');
        $response->assertSee('empty-slot');
    }

    /** @test */
    public function master_timetable_handles_invalid_weekday()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.room-management.master-timetable.show', 99));

        $response->assertStatus(404);
    }

    /** @test */
    public function admin_can_get_available_time_slots()
    {
        // Create a lesson that occupies 8:00-9:30 AM
        Lesson::factory()->create([
            'weekday' => 1, // Monday
            'room_id' => $this->room1->id,
            'start_time' => '08:00:00',
            'end_time' => '09:30:00'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.room-management.master-timetable.available-slots'), [
                'room_id' => $this->room1->id,
                'weekday' => 1,
                'duration' => 60
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'room',
            'weekday',
            'weekday_name',
            'duration',
            'available_slots',
            'total_available'
        ]);
    }

    /** @test */
    public function admin_can_get_room_utilization_stats()
    {
        // Create some lessons for Monday
        Lesson::factory()->create([
            'weekday' => 1,
            'room_id' => $this->room1->id,
            'start_time' => '08:00:00',
            'end_time' => '10:00:00' // 2 hours
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.room-management.master-timetable.room-utilization'), [
                'weekday' => 1
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'weekday',
            'weekday_name',
            'statistics' => [
                '*' => [
                    'room',
                    'total_lessons',
                    'total_minutes',
                    'utilization_percentage',
                    'utilization_status'
                ]
            ]
        ]);
    }

    /** @test */
    public function admin_can_get_quick_stats()
    {
        // Create some test data
        Lesson::factory()->count(5)->create([
            'weekday' => 1
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.room-management.master-timetable.quick-stats'), [
                'weekday' => 1
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'statistics' => [
                'total_lessons',
                'total_rooms',
                'total_classes',
                'total_teachers',
                'average_room_utilization',
                'weekday',
                'weekday_name'
            ]
        ]);
    }

    /** @test */
    public function admin_can_export_timetable_as_csv()
    {
        // Create some test lessons
        Lesson::factory()->create([
            'weekday' => 1,
            'class_id' => $this->class1->id,
            'teacher_id' => $this->teacher->id,
            'room_id' => $this->room1->id,
            'subject_id' => $this->subject1->id,
            'start_time' => '08:00:00',
            'end_time' => '09:30:00'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.room-management.master-timetable.export'), [
                'weekday' => 1,
                'format' => 'csv'
            ]);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition');
    }

    /** @test */
    public function admin_can_export_timetable_as_json()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.room-management.master-timetable.export'), [
                'weekday' => 1,
                'format' => 'json'
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'export_data' => [
                'weekday',
                'weekday_name',
                'rooms',
                'time_slots',
                'timetable_matrix',
                'statistics'
            ],
            'exported_at'
        ]);
    }

    /** @test */
    public function admin_can_get_lesson_details()
    {
        $lesson = Lesson::factory()->create([
            'weekday' => 1,
            'class_id' => $this->class1->id,
            'teacher_id' => $this->teacher->id,
            'room_id' => $this->room1->id,
            'subject_id' => $this->subject1->id,
            'start_time' => '08:00:00',
            'end_time' => '09:30:00'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.room-management.master-timetable.lesson-details'), [
                'lesson_id' => $lesson->id
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'lesson' => [
                'id',
                'class_name',
                'teacher_name',
                'subject_name',
                'room_name',
                'start_time',
                'end_time',
                'weekday',
                'weekday_name',
                'duration',
                'has_conflicts'
            ]
        ]);
    }

    /** @test */
    public function master_timetable_handles_conflicts_correctly()
    {
        // Create conflicting lessons
        Lesson::factory()->create([
            'weekday' => 1,
            'class_id' => $this->class1->id,
            'teacher_id' => $this->teacher->id,
            'room_id' => $this->room1->id,
            'subject_id' => $this->subject1->id,
            'start_time' => '08:00:00',
            'end_time' => '09:30:00'
        ]);

        // Try to create another lesson at the same time
        $response = $this->actingAs($this->admin)
            ->post(route('admin.room-management.master-timetable.check-conflicts'), [
                'weekday' => 1,
                'room_id' => $this->room1->id,
                'start_time' => '8:00 AM',
                'end_time' => '9:30 AM',
                'class_id' => $this->class2->id,
                'teacher_id' => $this->teacher->id,
                'subject_id' => $this->subject2->id
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'has_conflicts' => true
        ]);
    }

    /** @test */
    public function master_timetable_validates_request_data()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.room-management.master-timetable.check-conflicts'), []);

        $response->assertStatus(422);
    }

    /** @test */
    public function master_timetable_handles_multi_slot_lessons()
    {
        // Create a lesson that spans multiple time slots
        Lesson::factory()->create([
            'weekday' => 1,
            'class_id' => $this->class1->id,
            'teacher_id' => $this->teacher->id,
            'room_id' => $this->room1->id,
            'subject_id' => $this->subject1->id,
            'start_time' => '08:00:00',
            'end_time' => '11:00:00' // 3 hours - spans 6 time slots
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.room-management.master-timetable.show', 1));

        $response->assertStatus(200);
        // The lesson should be displayed across multiple cells
        $response->assertSee('Programming');
    }

    /** @test */
    public function master_timetable_shows_lab_indicators()
    {
        // Create a lesson in a lab room with a lab subject
        Lesson::factory()->create([
            'weekday' => 1,
            'class_id' => $this->class1->id,
            'teacher_id' => $this->teacher->id,
            'room_id' => $this->room1->id, // Lab room
            'subject_id' => $this->subject1->id, // Lab subject
            'start_time' => '08:00:00',
            'end_time' => '09:30:00'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.room-management.master-timetable.show', 1));

        $response->assertStatus(200);
        $response->assertSee('Lab Required');
        $response->assertSee('fa-flask');
    }

    /** @test */
    public function master_timetable_displays_statistics_correctly()
    {
        // Create some test lessons
        Lesson::factory()->create([
            'weekday' => 1,
            'room_id' => $this->room1->id,
            'start_time' => '08:00:00',
            'end_time' => '10:00:00'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.room-management.master-timetable.show', 1));

        $response->assertStatus(200);
        $response->assertSee('Total Rooms');
        $response->assertSee('Occupied Slots');
        $response->assertSee('Available Slots');
        $response->assertSee('Utilization');
        $response->assertSee('Active Rooms');
        $response->assertSee('Empty Rooms');
    }
}
