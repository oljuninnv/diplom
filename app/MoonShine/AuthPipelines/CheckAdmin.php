<?php

namespace App\MoonShine\AuthPipelines;
 
use Closure;
use Illuminate\Http\Request;
use App\Enums\UserRoleEnum;
use Illuminate\Support\Facades\Auth;
 
class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $values = $request->all();

        if (Auth::attempt(['email' => $values['username'], 'password' => $values['password']])) {
            $user = Auth::user();

            if ($user->role->name !== UserRoleEnum::ADMIN->value) {
                return redirect('/');
            }
        }
 
        return $next($request);
    }
}