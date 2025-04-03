<?php

declare(strict_types=1);

namespace App\Models;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use MoonShine\Permissions\Traits\HasMoonShinePermissions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory, HasMoonShinePermissions;

    protected $fillable = [
		'name',
		'avatar',
		'email',
		'phone',
		'role_id',
		'telegram_user_id',
        'password',
        'date_of_auth'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'date',
    ];

    public function getAvatarUrlAttribute()
    {
        return $this->avatar 
            ? Storage::url($this->avatar) 
            : 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class, 'telegram_user_id');
    }

    public function worker()
    {
        return $this->hasOne(Worker::class);
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }
}
