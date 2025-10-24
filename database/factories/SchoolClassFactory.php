<?php

namespace Database\Factories;

use App\SchoolClass;
use App\AcademicProgram;
use App\GradeLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\SchoolClass>
 */
class SchoolClassFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SchoolClass::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $program = AcademicProgram::factory()->create();
        $gradeLevel = GradeLevel::factory()->create(['program_id' => $program->id]);
        
        return [
            'name' => $this->faker->word() . ' ' . $this->faker->randomElement(['A', 'B', 'C']),
            'program_id' => $program->id,
            'grade_level_id' => $gradeLevel->id,
            'section' => $this->faker->randomElement(['A', 'B', 'C', 'D']),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the class is inactive.
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }
}