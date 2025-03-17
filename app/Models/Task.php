<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'task',
        'post_id',
        'level',
        'deadline',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}