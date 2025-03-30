<?php

declare(strict_types=1);

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Call extends Model
{
    protected $fillable = [
		'type',
		'meeting_link',
		'date',
		'time',
		'candidate_id',
		'tutor_id',
		'hr_manager_id',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    public function hr_manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hr_manager_id');
    }
}
