<?php

use App\Role;
use Illuminate\Database\Seeder;

class RoleUserTableSeeder extends Seeder
{
    public function run()
    {
        // Link the seeded Admin user (id = 1) to the Admin role (id = 1)
        Role::findOrFail(1)->rolesUsers()->sync([1]);
    }
}
