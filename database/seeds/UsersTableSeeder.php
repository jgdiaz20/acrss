<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id'             => 1,
                'name'           => 'Admin',
                'email'          => 'admin@admin.com', // Legacy admin account - exempt from .edu.ph validation
                'password'       => '$2y$10$HvSDJRBDVWwRd18qj5oaQOF0DBXqnZcyFJ4dJA8hcQGAfmyZ7xkei',
                'remember_token' => null,
                'class_id'       => null,
                'is_admin'       => true,
                'is_teacher'     => false,
                'is_student'     => false,
            ],
            [
                'id'             => 2,
                'name'           => 'Teacher',
                'email'          => 'teacher@school.edu.ph',
                'password'       => '$2y$10$HvSDJRBDVWwRd18qj5oaQOF0DBXqnZcyFJ4dJA8hcQGAfmyZ7xkei',
                'remember_token' => null,
                'class_id'       => null,
                'is_admin'       => false,
                'is_teacher'     => true,
                'is_student'     => false,
            ],
            [
                'id'             => 3,
                'name'           => 'Teacher 2',
                'email'          => 'teacher2@school.edu.ph',
                'password'       => '$2y$10$HvSDJRBDVWwRd18qj5oaQOF0DBXqnZcyFJ4dJA8hcQGAfmyZ7xkei',
                'remember_token' => null,
                'class_id'       => null,
                'is_admin'       => false,
                'is_teacher'     => true,
                'is_student'     => false,
            ],
            [
                'id'             => 4,
                'name'           => 'Teacher 3',
                'email'          => 'teacher3@school.edu.ph',
                'password'       => '$2y$10$HvSDJRBDVWwRd18qj5oaQOF0DBXqnZcyFJ4dJA8hcQGAfmyZ7xkei',
                'remember_token' => null,
                'class_id'       => null,
                'is_admin'       => false,
                'is_teacher'     => true,
                'is_student'     => false,
            ],
            [
                'id'             => 5,
                'name'           => 'Teacher 4',
                'email'          => 'teacher4@school.edu.ph',
                'password'       => '$2y$10$HvSDJRBDVWwRd18qj5oaQOF0DBXqnZcyFJ4dJA8hcQGAfmyZ7xkei',
                'remember_token' => null,
                'class_id'       => null,
                'is_admin'       => false,
                'is_teacher'     => true,
                'is_student'     => false,
            ],
            [
                'id'             => 6,
                'name'           => 'Teacher 5',
                'email'          => 'teacher5@school.edu.ph',
                'password'       => '$2y$10$HvSDJRBDVWwRd18qj5oaQOF0DBXqnZcyFJ4dJA8hcQGAfmyZ7xkei',
                'remember_token' => null,
                'class_id'       => null,
                'is_admin'       => false,
                'is_teacher'     => true,
                'is_student'     => false,
            ],
            [
                'id'             => 7,
                'name'           => 'Student',
                'email'          => 'student@school.edu.ph',
                'password'       => '$2y$10$HvSDJRBDVWwRd18qj5oaQOF0DBXqnZcyFJ4dJA8hcQGAfmyZ7xkei',
                'remember_token' => null,
                'class_id'       => 1,
                'is_admin'       => false,
                'is_teacher'     => false,
                'is_student'     => true,
            ],
        ];

        User::insert($users);
    }
}
