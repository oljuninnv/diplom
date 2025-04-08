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

    // В модель Call добавьте следующие scope-методы:

    public function scopeFutureMeetings($query)
    {
        return $query->where(function ($q) {
            $q->whereDate('date', '>', now()->format('Y-m-d'))
                ->orWhere(function ($q) {
                    $q->whereDate('date', now()->format('Y-m-d'))
                        ->whereTime('time', '>=', now()->format('H:i:s'));
                });
        });
    }

    public function scopeFilterByUserRole($query, $user)
    {
        if ($user->isTutorWorker()) {
            $query->where('tutor_id', $user->id);
        } elseif ($user->isAdmin()) {
            $query->where('hr_manager_id', $user->id);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        if (!$search)
            return $query;

        return $query->where(function ($q) use ($search) {
            $q->whereHas('candidate', fn($q) => $q->where('name', 'like', "%{$search}%"))
                ->orWhereHas('tutor', fn($q) => $q->where('name', 'like', "%{$search}%"))
                ->orWhereHas('hr_manager', fn($q) => $q->where('name', 'like', "%{$search}%"));
        });
    }

    public function scopeFilterByType($query, $type)
    {
        if ($type) {
            $query->where('type', $type);
        }
        return $query;
    }

    public function scopeFilterByDate($query, $date)
    {
        if ($date) {
            $query->where('date', $date);
        }
        return $query;
    }

    public function scopeOrderByDateTime($query, $sort)
    {
        $sortDirection = $sort === 'datetime_desc' ? 'desc' : 'asc';
        return $query->orderBy('date', $sortDirection)->orderBy('time', $sortDirection);
    }

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
