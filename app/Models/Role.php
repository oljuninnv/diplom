<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\UserRoleEnum;

class Role extends Model
{
    use HasFactory;

    public static function getIdByRole(UserRoleEnum $role): ?int
    {
        return self::where('name', $role->value)->value('id');
    }

    protected $fillable = [
        'name',
    ];

    public function Users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}