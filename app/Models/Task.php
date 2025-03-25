<?php

declare(strict_types=1);

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
		'title',
		'task',
		'post_id',
		'level',
		'deadline',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
