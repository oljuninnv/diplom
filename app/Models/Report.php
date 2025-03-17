<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'tutor_id',
        'user_id',
        'report',
    ];

    public function tutor()
    {
        return $this->belongsTo(Worker::class, 'tutor_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}