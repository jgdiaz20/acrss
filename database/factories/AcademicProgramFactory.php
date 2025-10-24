<?php

namespace Database\Factories;

use App\AcademicProgram;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\AcademicProgram>
 */
class AcademicProgramFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AcademicProgram::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $programs = [
            ['name' => 'Computer Science', 'code' => 'CS'],
            ['name' => 'Information Technology', 'code' => 'IT'],
            ['name' => 'Software Engineering', 'code' => 'SE'],
            ['name' => 'Data Science', 'code' => 'DS'],
            ['name' => 'Cybersecurity', 'code' => 'CY'],
            ['name' => 'Business Administration', 'code' => 'BA'],
            ['name' => 'Mathematics', 'code' => 'MATH'],
            ['name' => 'Physics', 'code' => 'PHY'],
            ['name' => 'Chemistry', 'code' => 'CHEM'],
            ['name' => 'Biology', 'code' => 'BIO'],
        ];
        
        $program = $this->faker->randomElement($programs);
        
        return [
            'name' => $program['name'],
            'code' => $program['code'],
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the program is inactive.
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