<?php

use App\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id'    => '1',
                'title' => 'user_management_access',
            ],
            [
                'id'    => '2',
                'title' => 'permission_create',
            ],
            [
                'id'    => '3',
                'title' => 'permission_edit',
            ],
            [
                'id'    => '4',
                'title' => 'permission_show',
            ],
            [
                'id'    => '5',
                'title' => 'permission_delete',
            ],
            [
                'id'    => '6',
                'title' => 'permission_access',
            ],
            [
                'id'    => '7',
                'title' => 'role_create',
            ],
            [
                'id'    => '8',
                'title' => 'role_edit',
            ],
            [
                'id'    => '9',
                'title' => 'role_show',
            ],
            [
                'id'    => '10',
                'title' => 'role_delete',
            ],
            [
                'id'    => '11',
                'title' => 'role_access',
            ],
            [
                'id'    => '12',
                'title' => 'user_create',
            ],
            [
                'id'    => '13',
                'title' => 'user_edit',
            ],
            [
                'id'    => '14',
                'title' => 'user_show',
            ],
            [
                'id'    => '15',
                'title' => 'user_delete',
            ],
            [
                'id'    => '16',
                'title' => 'user_access',
            ],
            [
                'id'    => '17',
                'title' => 'lesson_create',
            ],
            [
                'id'    => '18',
                'title' => 'lesson_edit',
            ],
            [
                'id'    => '19',
                'title' => 'lesson_show',
            ],
            [
                'id'    => '20',
                'title' => 'lesson_delete',
            ],
            [
                'id'    => '21',
                'title' => 'lesson_access',
            ],
            [
                'id'    => '22',
                'title' => 'school_class_create',
            ],
            [
                'id'    => '23',
                'title' => 'school_class_edit',
            ],
            [
                'id'    => '24',
                'title' => 'school_class_show',
            ],
            [
                'id'    => '25',
                'title' => 'school_class_delete',
            ],
            [
                'id'    => '26',
                'title' => 'school_class_access',
            ],
            [
                'id'    => '27',
                'title' => 'room_create',
            ],
            [
                'id'    => '28',
                'title' => 'room_edit',
            ],
            [
                'id'    => '29',
                'title' => 'room_show',
            ],
            [
                'id'    => '30',
                'title' => 'room_delete',
            ],
            [
                'id'    => '31',
                'title' => 'room_access',
            ],
            [
                'id'    => '32',
                'title' => 'academic_program_create',
            ],
            [
                'id'    => '33',
                'title' => 'academic_program_edit',
            ],
            [
                'id'    => '34',
                'title' => 'academic_program_show',
            ],
            [
                'id'    => '35',
                'title' => 'academic_program_delete',
            ],
            [
                'id'    => '36',
                'title' => 'academic_program_access',
            ],
            [
                'id'    => '37',
                'title' => 'grade_level_create',
            ],
            [
                'id'    => '38',
                'title' => 'grade_level_edit',
            ],
            [
                'id'    => '39',
                'title' => 'grade_level_show',
            ],
            [
                'id'    => '40',
                'title' => 'grade_level_delete',
            ],
            [
                'id'    => '41',
                'title' => 'grade_level_access',
            ],
            [
                'id'    => '42',
                'title' => 'subject_create',
            ],
            [
                'id'    => '43',
                'title' => 'subject_edit',
            ],
            [
                'id'    => '44',
                'title' => 'subject_show',
            ],
            [
                'id'    => '45',
                'title' => 'subject_delete',
            ],
            [
                'id'    => '46',
                'title' => 'subject_access',
            ],
            [
                'id'    => '47',
                'title' => 'teacher_subject_create',
            ],
            [
                'id'    => '48',
                'title' => 'teacher_subject_edit',
            ],
            [
                'id'    => '49',
                'title' => 'teacher_subject_show',
            ],
            [
                'id'    => '50',
                'title' => 'teacher_subject_delete',
            ],
            [
                'id'    => '51',
                'title' => 'teacher_subject_access',
            ],
        ];

        Permission::insert($permissions);
    }
}
