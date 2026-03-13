<?php

use App\Lesson;
use Illuminate\Database\Seeder;

class LessonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lessons = [
            [
                'teacher_id' => 2, // Teacher
                'class_id' => 1, // Grade 11 STEM-A
                'subject_id' => 1, // Computer Programming 1
                'room_id' => 1, // Room 101 (Computer Lab 1)
                'weekday' => 1, // Monday
                'start_time' => '10:00:00',
                'end_time' => '12:00:00',
                'lesson_type' => 'laboratory',
                'duration_hours' => 2.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 3, // Teacher 2
                'class_id' => 1, // Grade 11 STEM-A
                'subject_id' => 2, // Data Structures and Algorithms
                'room_id' => 2, // Room 102 (Computer Lab 2)
                'weekday' => 1, // Monday
                'start_time' => '12:00:00',
                'end_time' => '14:00:00',
                'lesson_type' => 'laboratory',
                'duration_hours' => 2.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 4, // Teacher 3
                'class_id' => 1, // Grade 11 STEM-A
                'subject_id' => 5, // Software Engineering
                'room_id' => 4, // Room 201 (Lecture Hall)
                'weekday' => 1, // Monday
                'start_time' => '14:00:00',
                'end_time' => '16:00:00',
                'lesson_type' => 'lecture',
                'duration_hours' => 2.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 3, // Teacher 2
                'class_id' => 2, // Grade 11 STEM-B
                'subject_id' => 3, // Database Management Systems
                'room_id' => 3, // Room 103 (Computer Lab 3)
                'weekday' => 1, // Monday
                'start_time' => '14:00:00',
                'end_time' => '16:00:00',
                'lesson_type' => 'laboratory',
                'duration_hours' => 2.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 5, // Teacher 4
                'class_id' => 1, // Grade 11 STEM-A
                'subject_id' => 6, // Mathematics
                'room_id' => 4, // Room 201 (Lecture Hall)
                'weekday' => 2, // Tuesday
                'start_time' => '08:00:00',
                'end_time' => '10:00:00',
                'lesson_type' => 'lecture',
                'duration_hours' => 2.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 2, // Teacher
                'class_id' => 1, // Grade 11 STEM-A
                'subject_id' => 4, // Computer Networks
                'room_id' => 1, // Room 101 (Computer Lab 1)
                'weekday' => 2, // Tuesday
                'start_time' => '10:00:00',
                'end_time' => '12:00:00',
                'lesson_type' => 'laboratory',
                'duration_hours' => 2.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 4, // Teacher 3
                'class_id' => 1, // Grade 11 STEM-A
                'subject_id' => 7, // Food and Beverage Management
                'room_id' => 1, // Room 101 (Computer Lab 1)
                'weekday' => 2, // Tuesday
                'start_time' => '12:00:00',
                'end_time' => '14:00:00',
                'lesson_type' => 'laboratory',
                'duration_hours' => 2.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 6, // Teacher 5
                'class_id' => 1, // Grade 11 STEM-A
                'subject_id' => 8, // English Communication
                'room_id' => 5, // Room 202 (Conference Room)
                'weekday' => 3, // Wednesday
                'start_time' => '10:00:00',
                'end_time' => '12:00:00',
                'lesson_type' => 'lecture',
                'duration_hours' => 2.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 2, // Teacher
                'class_id' => 1, // Grade 11 STEM-A
                'subject_id' => 13, // Capstone Project
                'room_id' => 2, // Room 102 (Computer Lab 2)
                'weekday' => 3, // Wednesday
                'start_time' => '12:00:00',
                'end_time' => '14:00:00',
                'lesson_type' => 'lecture',
                'duration_hours' => 2.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 3, // Teacher 2
                'class_id' => 1, // Grade 11 STEM-A
                'subject_id' => 13, // Capstone Project
                'room_id' => 3, // Room 103 (Computer Lab 3)
                'weekday' => 3, // Wednesday
                'start_time' => '14:00:00',
                'end_time' => '16:00:00',
                'lesson_type' => 'laboratory',
                'duration_hours' => 2.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 2, // Teacher
                'class_id' => 1, // Grade 11 STEM-A
                'subject_id' => 11, // Physical Education
                'room_id' => 6, // Room 301 (Library)
                'weekday' => 4, // Thursday
                'start_time' => '10:00:00',
                'end_time' => '12:00:00',
                'lesson_type' => 'laboratory',
                'duration_hours' => 2.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 3, // Teacher 2
                'class_id' => 1, // Grade 11 STEM-A
                'subject_id' => 12, // Research Methods
                'room_id' => 5, // Room 202 (Conference Room)
                'weekday' => 4, // Thursday
                'start_time' => '12:00:00',
                'end_time' => '14:00:00',
                'lesson_type' => 'lecture',
                'duration_hours' => 2.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 4, // Teacher 3
                'class_id' => 1, // Grade 11 STEM-A
                'subject_id' => 9, // Culinary Arts
                'room_id' => 1, // Room 101 (Computer Lab 1)
                'weekday' => 4, // Thursday
                'start_time' => '14:00:00',
                'end_time' => '16:00:00',
                'lesson_type' => 'laboratory',
                'duration_hours' => 2.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 3, // Teacher 2
                'class_id' => 1, // Grade 11 STEM-A
                'subject_id' => 10, // Hotel Operations Management
                'room_id' => 5, // Room 202 (Conference Room)
                'weekday' => 5, // Friday
                'start_time' => '08:00:00',
                'end_time' => '10:00:00',
                'lesson_type' => 'lecture',
                'duration_hours' => 2.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 2, // Teacher
                'class_id' => 1, // Grade 11 STEM-A
                'subject_id' => 13, // Capstone Project
                'room_id' => 2, // Room 102 (Computer Lab 2)
                'weekday' => 5, // Friday
                'start_time' => '10:00:00',
                'end_time' => '12:00:00',
                'lesson_type' => 'lecture',
                'duration_hours' => 2.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 6, // Teacher 5
                'class_id' => 1, // Grade 11 STEM-A
                'subject_id' => 13, // Capstone Project
                'room_id' => 3, // Room 103 (Computer Lab 3)
                'weekday' => 5, // Friday
                'start_time' => '12:00:00',
                'end_time' => '14:00:00',
                'lesson_type' => 'laboratory',
                'duration_hours' => 2.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Lesson::insert($lessons);
    }
}
