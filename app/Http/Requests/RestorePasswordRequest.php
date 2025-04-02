<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestorePasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Разрешаем всем менять пароль (так как проверка будет по user_id)
    }

    public function rules()
    {
        return [
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'Пароль обязателен для заполнения',
            'password.min' => 'Пароль должен быть не менее 8 символов',
            'password.confirmed' => 'Пароли не совпадают',
        ];
    }
}
