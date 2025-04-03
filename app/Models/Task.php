<?php

declare(strict_types=1);

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Task extends Model
{
    protected $fillable = [
		'title',
		'task',
		'post_id',
		'level',
		'deadline',
    ];

    public function getDocumentUrlAttribute()
  {
      return $this->document_path ? Storage::url($this->document_path) : null;
  }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
