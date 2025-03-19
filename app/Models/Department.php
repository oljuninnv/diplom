<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
    ];

    public function post()
    {
        return $this->belongsToMany(Post::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
