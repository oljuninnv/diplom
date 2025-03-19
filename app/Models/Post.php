<?php

declare(strict_types=1);

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
  use HasFactory;
  protected $fillable = [
    'name',
    'department_id',
  ];

  public function department(): BelongsTo
  {
    return $this->belongsTo(Department::class, 'department_id');
  }
}
