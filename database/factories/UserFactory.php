<?php

namespace Database\Factories;

use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'is_admin' => false,
            'is_teacher' => false,
            'is_student' => false,
        ];
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_admin' => true,
                'is_teacher' => false,
                'is_student' => false,
            ];
        });
    }

    /**
     * Indicate that the user is a teacher.
     */
    public function teacher()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_admin' => false,
                'is_teacher' => true,
                'is_student' => false,
            ];
        });
    }

    /**
     * Indicate that the user is a student.
     */
    public function student()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_admin' => false,
                'is_teacher' => false,
                'is_student' => true,
            ];
        });
    }
}