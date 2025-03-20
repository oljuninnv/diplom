<?php

declare(strict_types=1);

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
		'sender_id',
		'receiver_id',
		'message',
		'document',
    ];

    public function sender_user_id(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver_id_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
