<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Api\LoginRequest;
class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $success = $this->authService->login($request->only('email', 'password'));
            return response()->json($success, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout();
            return response()->json(['message' => 'Выход из профиля прошло успешно.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Выход произвести не удалось т.к. пользователь не авторизован.'], 403);
        }
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
            'token' => $request->user()->token()
        ]);
    }
}