<?php

declare(strict_types=1);

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vacancy extends Model
{
    protected $fillable = [
		'post_id',
		'description',
		'salary',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
