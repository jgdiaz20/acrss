<?php

use Illuminate\Database\Seeder;
use App\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subjects = [
            // Computer Engineering Subjects
            [
                'name' => 'Computer Programming 1',
                'code' => 'COMPROG1',
                'description' => 'Introduction to computer programming concepts and algorithms',
                'credits' => 3,
                'lecture_units' => 0,
                'lab_units' => 3,
                'scheduling_mode' => 'lab',
                'type' => 'major',
                'is_active' => true,
            ],
            [
                'name' => 'Data Structures and Algorithms',
                'code' => 'DATASTR',
                'description' => 'Study of fundamental data structures and algorithm design',
                'credits' => 3,
                'lecture_units' => 0,
                'lab_units' => 3,
                'scheduling_mode' => 'lab',
                'type' => 'major',
                'is_active' => true,
            ],
            [
                'name' => 'Database Management Systems',
                'code' => 'DBMS',
                'description' => 'Introduction to database design and management',
                'credits' => 3,
                'lecture_units' => 0,
                'lab_units' => 3,
                'scheduling_mode' => 'lab',
                'type' => 'major',
                'is_active' => true,
            ],
            [
                'name' => 'Computer Networks',
                'code' => 'NETWORKS',
                'description' => 'Fundamentals of computer networking and protocols',
                'credits' => 3,
                'lecture_units' => 0,
                'lab_units' => 3,
                'scheduling_mode' => 'lab',
                'type' => 'major',
                'is_active' => true,
            ],
            [
                'name' => 'Software Engineering',
                'code' => 'SOFTENG',
                'description' => 'Software development methodologies and practices',
                'credits' => 3,
                'lecture_units' => 3,
                'lab_units' => 0,
                'scheduling_mode' => 'lecture',
                'type' => 'major',
                'is_active' => true,
            ],
            
            // Hospitality Management Subjects
            [
                'name' => 'Food and Beverage Management',
                'code' => 'FBMGMT',
                'description' => 'Management of food and beverage operations',
                'credits' => 3,
                'lecture_units' => 0,
                'lab_units' => 3,
                'scheduling_mode' => 'lab',
                'type' => 'major',
                'is_active' => true,
            ],
            [
                'name' => 'Culinary Arts',
                'code' => 'CULINARY',
                'description' => 'Practical cooking techniques and food preparation',
                'credits' => 3,
                'lecture_units' => 0,
                'lab_units' => 3,
                'scheduling_mode' => 'lab',
                'type' => 'major',
                'is_active' => true,
            ],
            [
                'name' => 'Hotel Operations Management',
                'code' => 'HOTELOPS',
                'description' => 'Management of hotel operations and services',
                'credits' => 3,
                'lecture_units' => 3,
                'lab_units' => 0,
                'scheduling_mode' => 'lecture',
                'type' => 'major',
                'is_active' => true,
            ],
            [
                'name' => 'Event Management',
                'code' => 'EVENTMGMT',
                'description' => 'Planning and management of events and conferences',
                'credits' => 3,
                'lecture_units' => 3,
                'lab_units' => 0,
                'scheduling_mode' => 'lecture',
                'type' => 'minor',
                'is_active' => true,
            ],
            [
                'name' => 'Tourism and Travel Management',
                'code' => 'TOURISM',
                'description' => 'Introduction to tourism industry and travel management',
                'credits' => 3,
                'lecture_units' => 3,
                'lab_units' => 0,
                'scheduling_mode' => 'lecture',
                'type' => 'major',
                'is_active' => true,
            ],
            
            // General Subjects
            [
                'name' => 'Mathematics',
                'code' => 'MATH',
                'description' => 'General mathematics and problem solving',
                'credits' => 3,
                'lecture_units' => 3,
                'lab_units' => 0,
                'scheduling_mode' => 'lecture',
                'type' => 'major',
                'is_active' => true,
            ],
            [
                'name' => 'English Communication',
                'code' => 'ENGLISH',
                'description' => 'English language and communication skills',
                'credits' => 3,
                'lecture_units' => 3,
                'lab_units' => 0,
                'scheduling_mode' => 'lecture',
                'type' => 'major',
                'is_active' => true,
            ],
            [
                'name' => 'Physical Education',
                'code' => 'PE',
                'description' => 'Physical fitness and sports activities',
                'credits' => 2,
                'lecture_units' => 0,
                'lab_units' => 2,
                'scheduling_mode' => 'lab',
                'type' => 'major',
                'is_active' => true,
            ],
            [
                'name' => 'Research Methods',
                'code' => 'RESEARCH',
                'description' => 'Introduction to research methodologies and techniques',
                'credits' => 3,
                'lecture_units' => 3,
                'lab_units' => 0,
                'scheduling_mode' => 'lecture',
                'type' => 'major',
                'is_active' => true,
            ],
            [
                'name' => 'Capstone Project',
                'code' => 'CAPSTONE',
                'description' => 'Final project integrating all learned concepts',
                'credits' => 6,
                'lecture_units' => 3,
                'lab_units' => 3,
                'scheduling_mode' => 'flexible',
                'type' => 'major',
                'is_active' => true,
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}