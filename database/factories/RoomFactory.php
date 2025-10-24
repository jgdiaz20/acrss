<?php

namespace Database\Factories;

use App\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Room>
 */
class RoomFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Room::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $roomTypes = ['Classroom', 'Lab', 'Library', 'Auditorium', 'Computer Lab'];
        $roomType = $this->faker->randomElement($roomTypes);
        
        return [
            'name' => $roomType . ' ' . $this->faker->numberBetween(100, 999),
            'capacity' => $this->faker->numberBetween(20, 100),
            'is_lab' => $roomType === 'Lab' || $roomType === 'Computer Lab',
            'has_equipment' => $this->faker->boolean(70),
            'description' => $this->faker->sentence(),
        ];
    }

    /**
     * Indicate that the room is a lab.
     */
    public function lab()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_lab' => true,
                'has_equipment' => true,
                'name' => 'Lab ' . $this->faker->numberBetween(100, 999),
            ];
        });
    }

    /**
     * Indicate that the room is a regular classroom.
     */
    public function classroom()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_lab' => false,
                'has_equipment' => $this->faker->boolean(30),
                'name' => 'Classroom ' . $this->faker->numberBetween(100, 999),
            ];
        });
    }
}