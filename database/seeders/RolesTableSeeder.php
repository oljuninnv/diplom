<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Enums\UserRoleEnum;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => UserRoleEnum::USER->value],
            ['name' => UserRoleEnum::WORKER->value],
            ['name' => UserRoleEnum::TUTOR_WORKER->value],
            ['name' => UserRoleEnum::ADMIN->value],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}