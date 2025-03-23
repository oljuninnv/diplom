<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}
