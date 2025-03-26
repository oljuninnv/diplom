<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Enums\UserRoleEnum;

use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $roles = Role::all();

        $users = [
            [
                'name' => 'User',
                'email' => 'user1@example.com',
                'password' => Hash::make('password'),
                'phone' => '+79245678324',
                'role_id' => $roles->where('name', UserRoleEnum::USER->value)->first()->id,
            ],
            [
                'name' => 'Worker',
                'email' => 'worker1@example.com',
                'password' => Hash::make('password'),
                'phone' => '+79254252534',
                'role_id' => $roles->where('name', UserRoleEnum::WORKER->value)->first()->id,
            ],
            [
                'name' => 'Tutor',
                'email' => 'tutor1@example.com',
                'password' => Hash::make('password'),
                'phone' => '+79070740021',
                'role_id' => $roles->where('name', UserRoleEnum::TUTOR_WORKER->value)->first()->id,
            ],
            [
                'name' => 'Admin',
                'email' => 'admin1@example.com',
                'password' => Hash::make('password'),
                'phone' => '+79050760021',
                'role_id' => $roles->where('name', UserRoleEnum::ADMIN->value)->first()->id,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}