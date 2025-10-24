<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\MasterTimetableService;
use App\Services\TimeService;
use App\Services\SchedulingConflictService;
use App\Room;
use App\SchoolClass;
use App\User;
use App\Subject;
use App\Lesson;
use App\AcademicProgram;
use App\GradeLevel;
use App\TeacherSubject;

class MasterTimetableServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $masterTimetableService;
    protected $timeService;
    protected $schedulingConflictService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->timeService = new TimeService();
        $this->schedulingConflictService = new SchedulingConflictService();
        $this->masterTimetableService = new MasterTimetableService(
            $this->timeService,
            $this->schedulingConflictService
        );
    }

    /** @test */
    public function it_generates_master_timetable_data_for_weekday()
    {
        // Create test data
        $room = Room::factory()->create(['name' => 'Test Room']);
        $program = AcademicProgram::factory()->create();
        $gradeLevel = GradeLevel::factory()->create(['program_id' => $program->id]);
        $class = SchoolClass::factory()->create(['program_id' => $program->id, 'grade_level_id' => $gradeLevel->id]);
        $teacher = User::factory()->create(['is_teacher' => true]);
        $subject = Subject::factory()->create();

        $lesson = Lesson::factory()->create([
            'weekday' => 1,
            'room_id' => $room->id,
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'start_time' => '08:00:00',
            'end_time' => '09:30:00'
        ]);

        $result = $this->masterTimetableService->generateMasterTimetableData(1);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['weekday']);
        $this->assertEquals('Monday', $result['weekday_name']);
        $this->assertArrayHasKey('rooms', $result);
        $this->assertArrayHasKey('time_slots', $result);
        $this->assertArrayHasKey('timetable_matrix', $result);
        $this->assertArrayHasKey('statistics', $result);

        // Check that the lesson is included in the matrix
        $hasLesson = false;
        foreach ($result['timetable_matrix'] as $row) {
            foreach ($row['rooms'] as $roomData) {
                if ($roomData['type'] === 'lesson' && $roomData['lesson']->id === $lesson->id) {
                    $hasLesson = true;
                    break 2;
                }
            }
        }
        $this->assertTrue($hasLesson);
    }

    /** @test */
    public function it_identifies_empty_slots_correctly()
    {
        // Create a room but no lessons
        $room = Room::factory()->create(['name' => 'Empty Room']);

        $result = $this->masterTimetableService->generateMasterTimetableData(1);

        // Check that we have empty slots
        $hasEmptySlots = false;
        foreach ($result['timetable_matrix'] as $row) {
            foreach ($row['rooms'] as $roomData) {
                if ($roomData['type'] === 'empty') {
                    $hasEmptySlots = true;
                    $this->assertEquals('empty-slot available-for-scheduling', $roomData['css_class']);
                    break 2;
                }
            }
        }
        $this->assertTrue($hasEmptySlots);
    }

    /** @test */
    public function it_generates_statistics_correctly()
    {
        // Create test data
        $room1 = Room::factory()->create(['name' => 'Room 1']);
        $room2 = Room::factory()->create(['name' => 'Room 2']);
        $program = AcademicProgram::factory()->create();
        $gradeLevel = GradeLevel::factory()->create(['program_id' => $program->id]);
        $class = SchoolClass::factory()->create(['program_id' => $program->id, 'grade_level_id' => $gradeLevel->id]);
        $teacher = User::factory()->create(['is_teacher' => true]);
        $subject = Subject::factory()->create();

        // Create lessons in room1 only
        Lesson::factory()->create([
            'weekday' => 1,
            'room_id' => $room1->id,
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'start_time' => '08:00:00',
            'end_time' => '10:00:00'
        ]);

        $result = $this->masterTimetableService->generateMasterTimetableData(1);

        $stats = $result['statistics'];
        $this->assertEquals(2, $stats['total_rooms']);
        $this->assertGreaterThan(0, $stats['total_time_slots']);
        $this->assertGreaterThan(0, $stats['occupied_slots']);
        $this->assertGreaterThan(0, $stats['empty_slots']);
        $this->assertEquals(1, $stats['rooms_with_lessons']);
        $this->assertEquals(1, $stats['rooms_without_lessons']);
        $this->assertIsNumeric($stats['utilization_percentage']);
    }

    /** @test */
    public function it_gets_available_time_slots_correctly()
    {
        $room = Room::factory()->create(['name' => 'Test Room']);
        $program = AcademicProgram::factory()->create();
        $gradeLevel = GradeLevel::factory()->create(['program_id' => $program->id]);
        $class = SchoolClass::factory()->create(['program_id' => $program->id, 'grade_level_id' => $gradeLevel->id]);
        $teacher = User::factory()->create(['is_teacher' => true]);
        $subject = Subject::factory()->create();

        // Create a lesson that occupies 8:00-9:30 AM
        Lesson::factory()->create([
            'weekday' => 1,
            'room_id' => $room->id,
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'start_time' => '08:00:00',
            'end_time' => '09:30:00'
        ]);

        $availableSlots = $this->masterTimetableService->getAvailableTimeSlots($room->id, 1, 60);

        $this->assertIsArray($availableSlots);
        
        // Check that 8:00-9:30 slot is not available
        $conflictingSlot = collect($availableSlots)->first(function ($slot) {
            return $slot['start_time'] === '08:00' && $slot['end_time'] === '09:00';
        });
        $this->assertNull($conflictingSlot);

        // Check that other slots are available
        $this->assertGreaterThan(0, count($availableSlots));
    }

    /** @test */
    public function it_calculates_room_utilization_correctly()
    {
        $room = Room::factory()->create(['name' => 'Test Room']);
        $program = AcademicProgram::factory()->create();
        $gradeLevel = GradeLevel::factory()->create(['program_id' => $program->id]);
        $class = SchoolClass::factory()->create(['program_id' => $program->id, 'grade_level_id' => $gradeLevel->id]);
        $teacher = User::factory()->create(['is_teacher' => true]);
        $subject = Subject::factory()->create();

        // Create lessons totaling 3 hours (180 minutes) for Monday
        Lesson::factory()->create([
            'weekday' => 1,
            'room_id' => $room->id,
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'start_time' => '08:00:00',
            'end_time' => '11:00:00'
        ]);

        $stats = $this->masterTimetableService->getRoomUtilizationStats(1);

        $this->assertIsArray($stats);
        $this->assertCount(1, $stats);
        
        $roomStats = $stats[0];
        $this->assertEquals($room->id, $roomStats['room']->id);
        $this->assertEquals(1, $roomStats['total_lessons']);
        $this->assertEquals(180, $roomStats['total_minutes']);
        $this->assertGreaterThan(0, $roomStats['utilization_percentage']);
        $this->assertContains($roomStats['utilization_status'], ['low', 'medium', 'high']);
    }

    /** @test */
    public function it_handles_multi_slot_lessons_correctly()
    {
        $room = Room::factory()->create(['name' => 'Test Room']);
        $program = AcademicProgram::factory()->create();
        $gradeLevel = GradeLevel::factory()->create(['program_id' => $program->id]);
        $class = SchoolClass::factory()->create(['program_id' => $program->id, 'grade_level_id' => $gradeLevel->id]);
        $teacher = User::factory()->create(['is_teacher' => true]);
        $subject = Subject::factory()->create();

        // Create a 3-hour lesson (spans multiple time slots)
        Lesson::factory()->create([
            'weekday' => 1,
            'room_id' => $room->id,
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'start_time' => '08:00:00',
            'end_time' => '11:00:00'
        ]);

        $multiSlotLessons = $this->masterTimetableService->getMultiSlotLessons(1);

        $this->assertIsArray($multiSlotLessons);
        $this->assertCount(1, $multiSlotLessons);
        
        $multiSlotLesson = $multiSlotLessons[0];
        $this->assertEquals(180, $multiSlotLesson['duration_minutes']);
        $this->assertGreaterThan(1, $multiSlotLesson['slot_count']);
        $this->assertIsArray($multiSlotLesson['affected_slots']);
    }

    /** @test */
    public function it_formats_lesson_display_data_correctly()
    {
        $room = Room::factory()->create(['name' => 'Test Room']);
        $program = AcademicProgram::factory()->create();
        $gradeLevel = GradeLevel::factory()->create(['program_id' => $program->id]);
        $class = SchoolClass::factory()->create(['program_id' => $program->id, 'grade_level_id' => $gradeLevel->id]);
        $teacher = User::factory()->create(['is_teacher' => true]);
        $subject = Subject::factory()->create(['requires_lab' => true]);

        $lesson = Lesson::factory()->create([
            'weekday' => 1,
            'room_id' => $room->id,
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'start_time' => '08:00:00',
            'end_time' => '09:30:00'
        ]);

        $result = $this->masterTimetableService->generateMasterTimetableData(1);

        // Find the lesson in the matrix
        $lessonData = null;
        foreach ($result['timetable_matrix'] as $row) {
            foreach ($row['rooms'] as $roomData) {
                if ($roomData['type'] === 'lesson' && $roomData['lesson']->id === $lesson->id) {
                    $lessonData = $roomData['display_data'];
                    break 2;
                }
            }
        }

        $this->assertNotNull($lessonData);
        $this->assertEquals($lesson->id, $lessonData['id']);
        $this->assertEquals($subject->name, $lessonData['subject_name']);
        $this->assertEquals($subject->code, $lessonData['subject_code']);
        $this->assertEquals($class->display_name, $lessonData['class_name']);
        $this->assertEquals($teacher->name, $lessonData['teacher_name']);
        $this->assertEquals($room->display_name, $lessonData['room_name']);
        $this->assertTrue($lessonData['is_lab_required']);
    }

    /** @test */
    public function it_applies_correct_css_classes_for_lessons()
    {
        $room = Room::factory()->create(['name' => 'Test Room']);
        $program = AcademicProgram::factory()->create();
        $gradeLevel = GradeLevel::factory()->create(['program_id' => $program->id]);
        $class = SchoolClass::factory()->create(['program_id' => $program->id, 'grade_level_id' => $gradeLevel->id]);
        $teacher = User::factory()->create(['is_teacher' => true]);
        $subject = Subject::factory()->create(['requires_lab' => true]);

        // Create a long lesson (2+ hours)
        Lesson::factory()->create([
            'weekday' => 1,
            'room_id' => $room->id,
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'start_time' => '08:00:00',
            'end_time' => '10:30:00' // 2.5 hours
        ]);

        $result = $this->masterTimetableService->generateMasterTimetableData(1);

        // Find the lesson in the matrix
        $lessonCssClass = null;
        foreach ($result['timetable_matrix'] as $row) {
            foreach ($row['rooms'] as $roomData) {
                if ($roomData['type'] === 'lesson') {
                    $lessonCssClass = $roomData['css_class'];
                    break 2;
                }
            }
        }

        $this->assertNotNull($lessonCssClass);
        $this->assertStringContainsString('lesson-slot', $lessonCssClass);
        $this->assertStringContainsString('lab-lesson', $lessonCssClass);
        $this->assertStringContainsString('long-lesson', $lessonCssClass);
    }

    /** @test */
    public function it_handles_empty_rooms_correctly()
    {
        // Create rooms but no lessons
        Room::factory()->count(3)->create();

        $result = $this->masterTimetableService->generateMasterTimetableData(1);

        $stats = $result['statistics'];
        $this->assertEquals(3, $stats['total_rooms']);
        $this->assertEquals(0, $stats['occupied_slots']);
        $this->assertEquals(3 * count($result['time_slots']), $stats['empty_slots']);
        $this->assertEquals(0, $stats['rooms_with_lessons']);
        $this->assertEquals(3, $stats['rooms_without_lessons']);
        $this->assertEquals(0, $stats['utilization_percentage']);
    }

    /** @test */
    public function it_handles_weekend_days_gracefully()
    {
        // Create some rooms and lessons for Saturday (weekday 6)
        $room = Room::factory()->create(['name' => 'Weekend Room']);
        $program = AcademicProgram::factory()->create();
        $gradeLevel = GradeLevel::factory()->create(['program_id' => $program->id]);
        $class = SchoolClass::factory()->create(['program_id' => $program->id, 'grade_level_id' => $gradeLevel->id]);
        $teacher = User::factory()->create(['is_teacher' => true]);
        $subject = Subject::factory()->create();

        Lesson::factory()->create([
            'weekday' => 6, // Saturday
            'room_id' => $room->id,
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'start_time' => '08:00:00',
            'end_time' => '09:30:00'
        ]);

        $result = $this->masterTimetableService->generateMasterTimetableData(6);

        $this->assertIsArray($result);
        $this->assertEquals(6, $result['weekday']);
        $this->assertEquals('Saturday', $result['weekday_name']);
        
        // Should still find the lesson
        $hasLesson = false;
        foreach ($result['timetable_matrix'] as $row) {
            foreach ($row['rooms'] as $roomData) {
                if ($roomData['type'] === 'lesson') {
                    $hasLesson = true;
                    break 2;
                }
            }
        }
        $this->assertTrue($hasLesson);
    }
}
