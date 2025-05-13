<?php

declare(strict_types=1);

namespace App\Models;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use MoonShine\Permissions\Traits\HasMoonShinePermissions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use App\Enums\UserRoleEnum;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory, HasMoonShinePermissions;

    protected $fillable = [
        'name',
        'avatar',
        'email',
        'phone',
        'password',
        'date_of_auth'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'telegram_user_id',
        'role_id',
    ];

    protected $casts = [
        'email_verified_at' => 'date',
    ];

    /**
     * Scope для получения кандидатов
     */
    public function scopeCandidates(Builder $query): Builder
    {
        return $query->where('role_id', Role::getIdByRole(UserRoleEnum::USER));
    }

    /**
     * Scope для получения HR-менеджеров (админы и суперадмины)
     */
    public function scopeHrManagers(Builder $query): Builder
    {
        return $query->whereIn('role_id', [
            Role::getIdByRole(UserRoleEnum::ADMIN),
            Role::getIdByRole(UserRoleEnum::SUPER_ADMIN)
        ]);
    }

    /**
     * Scope для получения тьюторов
     */
    public function scopeTutors(Builder $query): Builder
    {
        return $query->where('role_id', Role::getIdByRole(UserRoleEnum::TUTOR_WORKER));
    }

    /**
     * Проверка, является ли пользователь тьютором
     */
    public function isTutorWorker(): bool
    {
        return $this->role->name === UserRoleEnum::TUTOR_WORKER->value;
    }

    /**
     * Проверка, является ли пользователь админом или суперадмином
     */
    public function isAdmin(): bool
    {
        return in_array($this->role->name, [
            UserRoleEnum::ADMIN->value, 
            UserRoleEnum::SUPER_ADMIN->value
        ]);
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar
            ? Storage::url($this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
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
