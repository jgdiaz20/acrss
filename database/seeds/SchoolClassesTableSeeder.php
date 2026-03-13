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
                'name' => 'Grade 11 STEM-A',
                'program_id' => 1, // STEM
                'grade_level_id' => 1, // Grade 11
                'section' => 'A',
                'max_students' => 35,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Grade 11 STEM-B',
                'program_id' => 1, // STEM
                'grade_level_id' => 1, // Grade 11
                'section' => 'B',
                'max_students' => 32,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Grade 12 STEM-A',
                'program_id' => 1, // STEM
                'grade_level_id' => 2, // Grade 12
                'section' => 'A',
                'max_students' => 30,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Senior High School - ABM Program
            [
                'name' => 'Grade 11 ABM-A',
                'program_id' => 2, // ABM
                'grade_level_id' => 1, // Grade 11
                'section' => 'A',
                'max_students' => 40,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Grade 12 ABM-A',
                'program_id' => 2, // ABM
                'grade_level_id' => 2, // Grade 12
                'section' => 'A',
                'max_students' => 38,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Senior High School - HUMSS Program
            [
                'name' => 'Grade 11 HUMSS-A',
                'program_id' => 3, // HUMSS
                'grade_level_id' => 1, // Grade 11
                'section' => 'A',
                'max_students' => 36,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Grade 12 HUMSS-A',
                'program_id' => 3, // HUMSS
                'grade_level_id' => 2, // Grade 12
                'section' => 'A',
                'max_students' => 34,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Senior High School - GAS Program
            [
                'name' => 'Grade 11 GAS-A',
                'program_id' => 4, // GAS
                'grade_level_id' => 1, // Grade 11
                'section' => 'A',
                'max_students' => 37,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Grade 12 GAS-A',
                'program_id' => 4, // GAS
                'grade_level_id' => 2, // Grade 12
                'section' => 'A',
                'max_students' => 35,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // College - Computer Engineering
            [
                'name' => 'CE 1st Year - Section 1',
                'program_id' => 5, // Computer Engineering
                'grade_level_id' => 3, // 1st Year College
                'section' => '1',
                'max_students' => 45,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'CE 1st Year - Section 2',
                'program_id' => 5, // Computer Engineering
                'grade_level_id' => 3, // 1st Year College
                'section' => '2',
                'max_students' => 42,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'CE 2nd Year - Section 1',
                'program_id' => 5, // Computer Engineering
                'grade_level_id' => 4, // 2nd Year College
                'section' => '1',
                'max_students' => 38,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'CE 3rd Year - Section 1',
                'program_id' => 5, // Computer Engineering
                'grade_level_id' => 5, // 3rd Year College
                'section' => '1',
                'max_students' => 35,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'CE 4th Year - Section 1',
                'program_id' => 5, // Computer Engineering
                'grade_level_id' => 6, // 4th Year College
                'section' => '1',
                'max_students' => 32,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // College - Hospitality Management
            [
                'name' => 'HM 1st Year - Section 1',
                'program_id' => 6, // Hospitality Management
                'grade_level_id' => 3, // 1st Year College
                'section' => '1',
                'max_students' => 40,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'HM 2nd Year - Section 1',
                'program_id' => 6, // Hospitality Management
                'grade_level_id' => 4, // 2nd Year College
                'section' => '1',
                'max_students' => 36,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'HM 3rd Year - Section 1',
                'program_id' => 6, // Hospitality Management
                'grade_level_id' => 5, // 3rd Year College
                'section' => '1',
                'max_students' => 34,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'HM 4th Year - Section 1',
                'program_id' => 6, // Hospitality Management
                'grade_level_id' => 6, // 4th Year College
                'section' => '1',
                'max_students' => 30,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // College - Information Technology
            [
                'name' => 'IT 1st Year - Section 1',
                'program_id' => 7, // Information Technology
                'grade_level_id' => 3, // 1st Year College
                'section' => '1',
                'max_students' => 50,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'IT 2nd Year - Section 1',
                'program_id' => 7, // Information Technology
                'grade_level_id' => 4, // 2nd Year College
                'section' => '1',
                'max_students' => 45,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'IT 3rd Year - Section 1',
                'program_id' => 7, // Information Technology
                'grade_level_id' => 5, // 3rd Year College
                'section' => '1',
                'max_students' => 40,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'IT 4th Year - Section 1',
                'program_id' => 7, // Information Technology
                'grade_level_id' => 6, // 4th Year College
                'section' => '1',
                'max_students' => 35,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // College - Business Administration
            [
                'name' => 'BA 1st Year - Section 1',
                'program_id' => 8, // Business Administration
                'grade_level_id' => 3, // 1st Year College
                'section' => '1',
                'max_students' => 48,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'BA 2nd Year - Section 1',
                'program_id' => 8, // Business Administration
                'grade_level_id' => 4, // 2nd Year College
                'section' => '1',
                'max_students' => 44,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'BA 3rd Year - Section 1',
                'program_id' => 8, // Business Administration
                'grade_level_id' => 5, // 3rd Year College
                'section' => '1',
                'max_students' => 40,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'BA 4th Year - Section 1',
                'program_id' => 8, // Business Administration
                'grade_level_id' => 6, // 4th Year College
                'section' => '1',
                'max_students' => 36,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Diploma - Information Technology
            [
                'name' => 'DIT 1st Year - Section 1',
                'program_id' => 9, // Diploma in IT
                'grade_level_id' => 7, // 1st Year Diploma
                'section' => '1',
                'max_students' => 45,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'DIT 2nd Year - Section 1',
                'program_id' => 9, // Diploma in IT
                'grade_level_id' => 8, // 2nd Year Diploma
                'section' => '1',
                'max_students' => 40,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'DIT 3rd Year - Section 1',
                'program_id' => 9, // Diploma in IT
                'grade_level_id' => 9, // 3rd Year Diploma
                'section' => '1',
                'max_students' => 35,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Diploma - Hospitality Technology
            [
                'name' => 'DHT 1st Year - Section 1',
                'program_id' => 10, // Diploma in Hospitality Technology
                'grade_level_id' => 7, // 1st Year Diploma
                'section' => '1',
                'max_students' => 38,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'DHT 2nd Year - Section 1',
                'program_id' => 10, // Diploma in Hospitality Technology
                'grade_level_id' => 8, // 2nd Year Diploma
                'section' => '1',
                'max_students' => 34,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'DHT 3rd Year - Section 1',
                'program_id' => 10, // Diploma in Hospitality Technology
                'grade_level_id' => 9, // 3rd Year Diploma
                'section' => '1',
                'max_students' => 30,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Diploma - Engineering Technology
            [
                'name' => 'DET 1st Year - Section 1',
                'program_id' => 11, // Diploma in Engineering Technology
                'grade_level_id' => 7, // 1st Year Diploma
                'section' => '1',
                'max_students' => 42,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'DET 2nd Year - Section 1',
                'program_id' => 11, // Diploma in Engineering Technology
                'grade_level_id' => 8, // 2nd Year Diploma
                'section' => '1',
                'max_students' => 38,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'DET 3rd Year - Section 1',
                'program_id' => 11, // Diploma in Engineering Technology
                'grade_level_id' => 9, // 3rd Year Diploma
                'section' => '1',
                'max_students' => 35,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        SchoolClass::insert($classes);
    }
}
