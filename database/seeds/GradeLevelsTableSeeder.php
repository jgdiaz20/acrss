<?php

use App\GradeLevel;
use Illuminate\Database\Seeder;

class GradeLevelsTableSeeder extends Seeder
{
    public function run()
    {
        $gradeLevels = [
            // Senior High School
            ['name' => 'Grade 11', 'level' => 11, 'program' => 'senior_high'],
            ['name' => 'Grade 12', 'level' => 12, 'program' => 'senior_high'],
            
            // College
            ['name' => '1st Year', 'level' => 1, 'program' => 'college'],
            ['name' => '2nd Year', 'level' => 2, 'program' => 'college'],
            ['name' => '3rd Year', 'level' => 3, 'program' => 'college'],
            ['name' => '4th Year', 'level' => 4, 'program' => 'college'],
        ];

        foreach ($gradeLevels as $gradeLevel) {
            GradeLevel::create($gradeLevel);
        }
    }
}
