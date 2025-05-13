<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * Class AuthService
 */
class AuthService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param array<string, mixed> $credentials
     * @return array
     */
    public function login(array $credentials): array
    {
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return [
                'token' => $user->createToken('User Token')->accessToken,
                'user' => $user,
            ];
        }

        throw new \Exception('Ошибка в заполнении данных');
    }

    public function logout(): void
    {
        $user = Auth::user();
        $this->userRepository->deleteTokens($user);
    }
}