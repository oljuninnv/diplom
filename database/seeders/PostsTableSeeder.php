<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Department;

class PostsTableSeeder extends Seeder
{
    public function run()
    {
        $departments = Department::all();

        $posts = [
            // HR
            ['name' => 'HR-менеджер', 'department_id' => $departments->where('name', 'HR')->first()->id],
            ['name' => 'Рекрутер', 'department_id' => $departments->where('name', 'HR')->first()->id],
            ['name' => 'HR-аналитик', 'department_id' => $departments->where('name', 'HR')->first()->id],
            ['name' => 'HR-директор', 'department_id' => $departments->where('name', 'HR')->first()->id],
            ['name' => 'HR-ассистент', 'department_id' => $departments->where('name', 'HR')->first()->id],

            // Разработка
            ['name' => 'Frontend-разработчик', 'department_id' => $departments->where('name', 'Разработка')->first()->id],
            ['name' => 'Backend-разработчик', 'department_id' => $departments->where('name', 'Разработка')->first()->id],
            ['name' => 'Fullstack-разработчик', 'department_id' => $departments->where('name', 'Разработка')->first()->id],
            ['name' => 'DevOps-инженер', 'department_id' => $departments->where('name', 'Разработка')->first()->id],
            ['name' => 'Техлид', 'department_id' => $departments->where('name', 'Разработка')->first()->id],

            // Тестирование
            ['name' => 'QA-инженер', 'department_id' => $departments->where('name', 'Тестирование')->first()->id],
            ['name' => 'Автоматизатор', 'department_id' => $departments->where('name', 'Тестирование')->first()->id],
            ['name' => 'Тестировщик', 'department_id' => $departments->where('name', 'Тестирование')->first()->id],
            ['name' => 'QA-лид', 'department_id' => $departments->where('name', 'Тестирование')->first()->id],
            ['name' => 'QA-аналитик', 'department_id' => $departments->where('name', 'Тестирование')->first()->id],

            // Дизайн
            ['name' => 'UI/UX-дизайнер', 'department_id' => $departments->where('name', 'Дизайн')->first()->id],
            ['name' => 'Графический дизайнер', 'department_id' => $departments->where('name', 'Дизайн')->first()->id],
            ['name' => 'Веб-дизайнер', 'department_id' => $departments->where('name', 'Дизайн')->first()->id],
            ['name' => 'Дизайнер интерфейсов', 'department_id' => $departments->where('name', 'Дизайн')->first()->id],
            ['name' => 'Арт-директор', 'department_id' => $departments->where('name', 'Дизайн')->first()->id],

            // Маркетинг
            ['name' => 'Маркетолог', 'department_id' => $departments->where('name', 'Маркетинг')->first()->id],
            ['name' => 'SMM-специалист', 'department_id' => $departments->where('name', 'Маркетинг')->first()->id],
            ['name' => 'Контент-менеджер', 'department_id' => $departments->where('name', 'Маркетинг')->first()->id],
            ['name' => 'SEO-специалист', 'department_id' => $departments->where('name', 'Маркетинг')->first()->id],
            ['name' => 'Аналитик маркетинга', 'department_id' => $departments->where('name', 'Маркетинг')->first()->id],
        ];

        foreach ($posts as $post) {
            Post::create($post);
        }
    }
}
