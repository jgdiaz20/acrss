<?php

use App\SchoolClass;
use Illuminate\Database\Seeder;

class SchoolClassesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $classes = [
            // Senior High School - STEM Program
            [
                'id' => 1,
                'name' => 'Grade 11 STEM-A',
                'program_id' => 1, // STEM
                'grade_level_id' => 1, // Grade 11
                'section' => 'A',
                'max_students' => 35,
                'is_active' => true,
            ],
            [
                'id' => 2,
                'name' => 'Grade 11 STEM-B',
                'program_id' => 1, // STEM
                'grade_level_id' => 1, // Grade 11
                'section' => 'B',
                'max_students' => 32,
                'is_active' => true,
            ],
            [
                'id' => 3,
                'name' => 'Grade 12 STEM-A',
                'program_id' => 1, // STEM
                'grade_level_id' => 2, // Grade 12
                'section' => 'A',
                'max_students' => 30,
                'is_active' => true,
            ],

            // Senior High School - ABM Program
            [
                'id' => 4,
                'name' => 'Grade 11 ABM-A',
                'program_id' => 2, // ABM
                'grade_level_id' => 1, // Grade 11
                'section' => 'A',
                'max_students' => 40,
                'is_active' => true,
            ],
            [
                'id' => 5,
                'name' => 'Grade 12 ABM-A',
                'program_id' => 2, // ABM
                'grade_level_id' => 2, // Grade 12
                'section' => 'A',
                'max_students' => 38,
                'is_active' => true,
            ],

            // Senior High School - HUMSS Program
            [
                'id' => 6,
                'name' => 'Grade 11 HUMSS-A',
                'program_id' => 3, // HUMSS
                'grade_level_id' => 1, // Grade 11
                'section' => 'A',
                'max_students' => 36,
                'is_active' => true,
            ],
            [
                'id' => 7,
                'name' => 'Grade 12 HUMSS-A',
                'program_id' => 3, // HUMSS
                'grade_level_id' => 2, // Grade 12
                'section' => 'A',
                'max_students' => 34,
                'is_active' => true,
            ],

            // College - Computer Engineering
            [
                'id' => 8,
                'name' => 'CE 1st Year - Section 1',
                'program_id' => 5, // Computer Engineering
                'grade_level_id' => 3, // 1st Year College
                'section' => '1',
                'max_students' => 45,
                'is_active' => true,
            ],
            [
                'id' => 9,
                'name' => 'CE 1st Year - Section 2',
                'program_id' => 5, // Computer Engineering
                'grade_level_id' => 3, // 1st Year College
                'section' => '2',
                'max_students' => 42,
                'is_active' => true,
            ],
            [
                'id' => 10,
                'name' => 'CE 2nd Year - Section 1',
                'program_id' => 5, // Computer Engineering
                'grade_level_id' => 4, // 2nd Year College
                'section' => '1',
                'max_students' => 38,
                'is_active' => true,
            ],
            [
                'id' => 11,
                'name' => 'CE 3rd Year - Section 1',
                'program_id' => 5, // Computer Engineering
                'grade_level_id' => 5, // 3rd Year College
                'section' => '1',
                'max_students' => 35,
                'is_active' => true,
            ],
            [
                'id' => 12,
                'name' => 'CE 4th Year - Section 1',
                'program_id' => 5, // Computer Engineering
                'grade_level_id' => 6, // 4th Year College
                'section' => '1',
                'max_students' => 32,
                'is_active' => true,
            ],

            // College - Hospitality Management
            [
                'id' => 13,
                'name' => 'HM 1st Year - Section 1',
                'program_id' => 6, // Hospitality Management
                'grade_level_id' => 3, // 1st Year College
                'section' => '1',
                'max_students' => 40,
                'is_active' => true,
            ],
            [
                'id' => 14,
                'name' => 'HM 2nd Year - Section 1',
                'program_id' => 6, // Hospitality Management
                'grade_level_id' => 4, // 2nd Year College
                'section' => '1',
                'max_students' => 36,
                'is_active' => true,
            ],
            [
                'id' => 15,
                'name' => 'HM 3rd Year - Section 1',
                'program_id' => 6, // Hospitality Management
                'grade_level_id' => 5, // 3rd Year College
                'section' => '1',
                'max_students' => 34,
                'is_active' => true,
            ],

            // College - Information Technology
            [
                'id' => 16,
                'name' => 'IT 1st Year - Section 1',
                'program_id' => 7, // Information Technology
                'grade_level_id' => 3, // 1st Year College
                'section' => '1',
                'max_students' => 50,
                'is_active' => true,
            ],
            [
                'id' => 17,
                'name' => 'IT 2nd Year - Section 1',
                'program_id' => 7, // Information Technology
                'grade_level_id' => 4, // 2nd Year College
                'section' => '1',
                'max_students' => 45,
                'is_active' => true,
            ],

            // College - Business Administration
            [
                'id' => 18,
                'name' => 'BA 1st Year - Section 1',
                'program_id' => 8, // Business Administration
                'grade_level_id' => 3, // 1st Year College
                'section' => '1',
                'max_students' => 48,
                'is_active' => true,
            ],
            [
                'id' => 19,
                'name' => 'BA 2nd Year - Section 1',
                'program_id' => 8, // Business Administration
                'grade_level_id' => 4, // 2nd Year College
                'section' => '1',
                'max_students' => 44,
                'is_active' => true,
            ],

            // Inactive/Archived Classes (for demonstration)
            [
                'id' => 20,
                'name' => 'CE 4th Year - Section 2 (Graduated)',
                'program_id' => 5, // Computer Engineering
                'grade_level_id' => 6, // 4th Year College
                'section' => '2',
                'max_students' => 30,
                'is_active' => false, // Archived class
            ],
        ];

        SchoolClass::insert($classes);
    }
}
