<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use MoonShine\Permissions\Traits\HasMoonShinePermissions;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory, HasMoonShinePermissions;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role_id',
        'post_id',
        'password',
        'avatar',
        'telegram_user_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function telegramUser()
    {
        return $this->belongsTo(TelegramUser::class);
    }
}