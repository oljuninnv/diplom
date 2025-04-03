<?php

declare(strict_types=1);

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
		'tutor_id',
		'user_id',
        'task_id',
		'report',
    ];

    public function tutor()
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    public function candidate()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function task()
    {
        return $this->belongsTo(TaskStatus::class, 'task_id');
    }
}
