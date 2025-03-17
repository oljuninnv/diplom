<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'description',
        'salary',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}