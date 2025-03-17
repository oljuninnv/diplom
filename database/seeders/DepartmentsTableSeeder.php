<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentsTableSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            ['name' => 'HR'],
            ['name' => 'Разработка'],
            ['name' => 'Тестирование'],
            ['name' => 'Дизайн'],
            ['name' => 'Маркетинг'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}