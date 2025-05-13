<?php

namespace App\Repositories;

use App\Models\User;

/**
 * Class UserRepository
 */
class UserRepository
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function findByName(string $name): ?User
    {
        return User::where('name', $name)->first();
    }

    public function deleteTokens(User $user): void
    {
        $user->tokens()->delete();
    }
}