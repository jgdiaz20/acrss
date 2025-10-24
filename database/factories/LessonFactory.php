<?php

namespace Database\Factories;

use App\Lesson;
use App\Room;
use App\SchoolClass;
use App\User;
use App\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Lesson::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $startHour = $this->faker->numberBetween(7, 16); // 7 AM to 4 PM
        $startTime = sprintf('%02d:%02d:00', $startHour, $this->faker->randomElement([0, 30]));
        $duration = $this->faker->randomElement([60, 90, 120]); // 1, 1.5, or 2 hours
        $endTime = date('H:i:s', strtotime($startTime . ' +' . $duration . ' minutes'));

        return [
            'weekday' => $this->faker->numberBetween(1, 5), // Monday to Friday
            'start_time' => $startTime,
            'end_time' => $endTime,
            'class_id' => SchoolClass::factory(),
            'teacher_id' => User::factory()->teacher(),
            'room_id' => Room::factory(),
            'subject_id' => Subject::factory(),
        ];
    }

    /**
     * Create a lesson for a specific weekday.
     */
    public function forWeekday(int $weekday)
    {
        return $this->state(function (array $attributes) use ($weekday) {
            return [
                'weekday' => $weekday,
            ];
        });
    }

    /**
     * Create a lesson in a lab room.
     */
    public function inLab()
    {
        return $this->state(function (array $attributes) {
            return [
                'room_id' => Room::factory()->lab(),
                'subject_id' => Subject::factory()->lab(),
            ];
        });
    }

    /**
     * Create a lesson in a regular classroom.
     */
    public function inClassroom()
    {
        return $this->state(function (array $attributes) {
            return [
                'room_id' => Room::factory()->classroom(),
                'subject_id' => Subject::factory()->classroom(),
            ];
        });
    }

    /**
     * Create a long lesson (2+ hours).
     */
    public function long()
    {
        return $this->state(function (array $attributes) {
            $startTime = $attributes['start_time'] ?? '08:00:00';
            $endTime = date('H:i:s', strtotime($startTime . ' +120 minutes'));
            
            return [
                'start_time' => $startTime,
                'end_time' => $endTime,
            ];
        });
    }

    /**
     * Create a short lesson (30 minutes).
     */
    public function short()
    {
        return $this->state(function (array $attributes) {
            $startTime = $attributes['start_time'] ?? '08:00:00';
            $endTime = date('H:i:s', strtotime($startTime . ' +30 minutes'));
            
            return [
                'start_time' => $startTime,
                'end_time' => $endTime,
            ];
        });
    }
}