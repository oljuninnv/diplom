<?php

declare(strict_types=1);

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskStatus extends Model
{
    protected $fillable = [
		'user_id',
		'tutor_id',
		'hr_manager_id',
		'task_id',
		'github_repo',
		'status',
		'end_date'
    ];

    protected $casts = [
        'end_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    public function hr_manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hr_manager_id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
