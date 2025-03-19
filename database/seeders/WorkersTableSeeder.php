<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Worker;
use App\Models\User;
use App\Models\Post;

class WorkersTableSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $posts = Post::all();

        $workers = [
            [
                'user_id' => $users->where('name', 'Worker')->first()->id,
                'department_id' => 2,
                'post_id' => $posts->where('name', 'Frontend-разработчик')->first()->id,
                'hire_date' => now(),
                'level_of_experience' => 'middle',
            ],
            [
                'user_id' => $users->where('name', 'Tutor')->first()->id,
                'department_id' => 3,
                'post_id' => $posts->where('name', 'QA-лид')->first()->id,
                'hire_date' => now(),
                'level_of_experience' => 'senior',
            ],
            [
                'user_id' => $users->where('name', 'Admin')->first()->id,
                'department_id' => 1,
                'post_id' => $posts->where('name', 'HR-директор')->first()->id,
                'hire_date' => now(),
                'level_of_experience' => 'senior',
            ],
        ];

        foreach ($workers as $worker) {
            Worker::create($worker);
        }
    }
}