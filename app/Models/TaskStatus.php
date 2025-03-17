<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tutor_id',
        'hr_manager_id',
        'task_id',
        'github_repo',
        'status',
        'end_date',
        'number_of_requests',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tutor()
    {
        return $this->belongsTo(Worker::class, 'tutor_id');
    }

    public function hrManager()
    {
        return $this->belongsTo(Worker::class, 'hr_manager_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}