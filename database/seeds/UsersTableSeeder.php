<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name'           => 'Admin',
                'email'          => 'admin@asiancollege.edu.ph', // Legacy admin account - exempt from .edu.ph validation
                'password'       => '$2y$10$HvSDJRBDVWwRd18qj5oaQOF0DBXqnZcyFJ4dJA8hcQGAfmyZ7xkei',
                'remember_token' => null,
                'class_id'       => null,
                'is_admin'       => true,
                'is_teacher'     => false,
                'is_student'     => false,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ];

        User::insert($users);
    }
}
