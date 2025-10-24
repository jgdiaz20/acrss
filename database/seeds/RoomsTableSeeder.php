<?php

use Illuminate\Database\Seeder;
use App\Room;

class RoomsTableSeeder extends Seeder
{
    public function run()
    {
        $rooms = [
            [
                'name' => 'Room 101',
                'description' => 'Computer Lab 1',
                'capacity' => 30,
                'is_lab' => true,
                'has_equipment' => true,
            ],
            [
                'name' => 'Room 102',
                'description' => 'Computer Lab 2',
                'capacity' => 25,
                'is_lab' => true,
                'has_equipment' => true,
            ],
            [
                'name' => 'Room 103',
                'description' => 'Computer Lab 3',
                'capacity' => 20,
                'is_lab' => true,
                'has_equipment' => true,
            ],
            [
                'name' => 'Room 201',
                'description' => 'Lecture Hall',
                'capacity' => 50,
                'is_lab' => false,
                'has_equipment' => true,
            ],
            [
                'name' => 'Room 202',
                'description' => 'Conference Room',
                'capacity' => 15,
                'is_lab' => false,
                'has_equipment' => true,
            ],
            [
                'name' => 'Room 301',
                'description' => 'Library',
                'capacity' => 40,
                'is_lab' => false,
                'has_equipment' => false,
            ],
            [
                'name' => 'Room 302',
                'description' => 'Study Room',
                'capacity' => 12,
                'is_lab' => false,
                'has_equipment' => false,
            ],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
