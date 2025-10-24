<?php

namespace Database\Factories;

use App\TeacherSubject;
use App\User;
use App\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\TeacherSubject>
 */
class TeacherSubjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TeacherSubject::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'teacher_id' => User::factory()->teacher(),
            'subject_id' => Subject::factory(),
            'is_active' => true,
            'is_primary' => $this->faker->boolean(70), // 70% chance of being primary
            'assigned_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the assignment is inactive.
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }

    /**
     * Indicate that the teacher is the primary teacher for this subject.
     */
    public function primary()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_primary' => true,
            ];
        });
    }

    /**
     * Indicate that the teacher is a secondary teacher for this subject.
     */
    public function secondary()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_primary' => false,
            ];
        });
    }
}