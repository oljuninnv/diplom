<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;
    protected $fillable = 
    [
      'name',
    ];

    public function posts(): BelongsToMany
  {
    return $this->BelongsToMany(Post::class);
  }

  public function workers(): HasMany
    {
        return $this->hasMany(Worker::class, 'department_id');
    }
}
