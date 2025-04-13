<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRoleEnum;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        // Преобразуем названия ролей из маршрута в значения enum
        $allowedRoles = array_map(function($role) {
            $role = strtoupper(str_replace('-', '_', $role));
            return constant(UserRoleEnum::class . '::' . $role)->value;
        }, $roles);

        if (!in_array($user->role->name, $allowedRoles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}