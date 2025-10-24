<?php

use Illuminate\Database\Seeder;
use App\GradeLevel;
use App\AcademicProgram;

class GradeLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all programs
        $programs = AcademicProgram::all();

        foreach ($programs as $program) {
            if ($program->type === 'senior_high') {
                // Senior High School - Only Grade 11 and Grade 12 (2 years)
                $gradeLevels = [
                    [
                        'program_id' => $program->id,
                        'level_name' => 'Grade 11',
                        'level_code' => 'G11',
                        'level_order' => 1,
                        'description' => 'First year of Senior High School',
                        'is_active' => true,
                    ],
                    [
                        'program_id' => $program->id,
                        'level_name' => 'Grade 12',
                        'level_code' => 'G12',
                        'level_order' => 2,
                        'description' => 'Second year of Senior High School',
                        'is_active' => true,
                    ],
                ];
            } elseif ($program->type === 'diploma') {
                // Diploma Program (TESDA) - 1st Year to 3rd Year (3 years)
                $gradeLevels = [
                    [
                        'program_id' => $program->id,
                        'level_name' => '1st Year',
                        'level_code' => '1Y',
                        'level_order' => 1,
                        'description' => 'First year of ' . $program->name,
                        'is_active' => true,
                    ],
                    [
                        'program_id' => $program->id,
                        'level_name' => '2nd Year',
                        'level_code' => '2Y',
                        'level_order' => 2,
                        'description' => 'Second year of ' . $program->name,
                        'is_active' => true,
                    ],
                    [
                        'program_id' => $program->id,
                        'level_name' => '3rd Year',
                        'level_code' => '3Y',
                        'level_order' => 3,
                        'description' => 'Third year of ' . $program->name,
                        'is_active' => true,
                    ],
                ];
            } else {
                // College - 1st Year to 4th Year (4 years)
                $gradeLevels = [
                    [
                        'program_id' => $program->id,
                        'level_name' => '1st Year',
                        'level_code' => '1Y',
                        'level_order' => 1,
                        'description' => 'First year of ' . $program->name,
                        'is_active' => true,
                    ],
                    [
                        'program_id' => $program->id,
                        'level_name' => '2nd Year',
                        'level_code' => '2Y',
                        'level_order' => 2,
                        'description' => 'Second year of ' . $program->name,
                        'is_active' => true,
                    ],
                    [
                        'program_id' => $program->id,
                        'level_name' => '3rd Year',
                        'level_code' => '3Y',
                        'level_order' => 3,
                        'description' => 'Third year of ' . $program->name,
                        'is_active' => true,
                    ],
                    [
                        'program_id' => $program->id,
                        'level_name' => '4th Year',
                        'level_code' => '4Y',
                        'level_order' => 4,
                        'description' => 'Fourth year of ' . $program->name,
                        'is_active' => true,
                    ],
                ];
            }

            foreach ($gradeLevels as $gradeLevel) {
                GradeLevel::create($gradeLevel);
            }
        }
    }
}