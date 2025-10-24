<?php

namespace Database\Factories;

use App\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subject::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $subjects = [
            ['name' => 'Mathematics', 'code' => 'MATH101', 'requires_lab' => false],
            ['name' => 'English', 'code' => 'ENG101', 'requires_lab' => false],
            ['name' => 'Science', 'code' => 'SCI101', 'requires_lab' => true],
            ['name' => 'Computer Science', 'code' => 'CS101', 'requires_lab' => true],
            ['name' => 'Physics', 'code' => 'PHY101', 'requires_lab' => true],
            ['name' => 'Chemistry', 'code' => 'CHEM101', 'requires_lab' => true],
            ['name' => 'Biology', 'code' => 'BIO101', 'requires_lab' => true],
            ['name' => 'History', 'code' => 'HIST101', 'requires_lab' => false],
            ['name' => 'Geography', 'code' => 'GEO101', 'requires_lab' => false],
        ];
        
        $subject = $this->faker->randomElement($subjects);
        
        return [
            'name' => $subject['name'],
            'code' => $subject['code'],
            'requires_lab' => $subject['requires_lab'],
            'requires_equipment' => $subject['requires_lab'],
            'is_active' => true,
            'description' => $this->faker->sentence(),
        ];
    }

    /**
     * Indicate that the subject requires a lab.
     */
    public function lab()
    {
        return $this->state(function (array $attributes) {
            return [
                'requires_lab' => true,
                'requires_equipment' => true,
            ];
        });
    }

    /**
     * Indicate that the subject does not require a lab.
     */
    public function classroom()
    {
        return $this->state(function (array $attributes) {
            return [
                'requires_lab' => false,
                'requires_equipment' => false,
            ];
        });
    }
}