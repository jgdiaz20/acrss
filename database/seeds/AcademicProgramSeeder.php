<?php

use Illuminate\Database\Seeder;
use App\AcademicProgram;

class AcademicProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $programs = [
            // Senior High School Programs
            [
                'name' => 'Science, Technology, Engineering, and Mathematics',
                'code' => 'STEM',
                'type' => 'senior_high',
                'duration_years' => 2,
                'description' => 'STEM strand focuses on science, technology, engineering, and mathematics',
                'is_active' => true,
            ],
            [
                'name' => 'Accountancy, Business and Management',
                'code' => 'ABM',
                'type' => 'senior_high',
                'duration_years' => 2,
                'description' => 'ABM strand focuses on business, management, and accounting',
                'is_active' => true,
            ],
            [
                'name' => 'Humanities and Social Sciences',
                'code' => 'HUMSS',
                'type' => 'senior_high',
                'duration_years' => 2,
                'description' => 'HUMSS strand focuses on humanities and social sciences',
                'is_active' => true,
            ],
            [
                'name' => 'General Academic Strand',
                'code' => 'GAS',
                'type' => 'senior_high',
                'duration_years' => 2,
                'description' => 'GAS strand provides a general academic track',
                'is_active' => true,
            ],
            
            // College Programs
            [
                'name' => 'Computer Engineering',
                'code' => 'CE',
                'type' => 'college',
                'duration_years' => 4,
                'description' => 'Bachelor of Science in Computer Engineering',
                'is_active' => true,
            ],
            [
                'name' => 'Hospitality Management',
                'code' => 'HM',
                'type' => 'college',
                'duration_years' => 4,
                'description' => 'Bachelor of Science in Hospitality Management',
                'is_active' => true,
            ],
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'type' => 'college',
                'duration_years' => 4,
                'description' => 'Bachelor of Science in Information Technology',
                'is_active' => true,
            ],
            [
                'name' => 'Business Administration',
                'code' => 'BA',
                'type' => 'college',
                'duration_years' => 4,
                'description' => 'Bachelor of Science in Business Administration',
                'is_active' => true,
            ],
        ];

        foreach ($programs as $program) {
            AcademicProgram::create($program);
        }
    }
}