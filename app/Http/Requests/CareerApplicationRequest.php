<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Department;

class CareerApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    // Проверяем наличие недопустимых символов
                    if (preg_match('/[^\d\+]/', $value)) {
                        $fail('Номер телефона может содержать только цифры и символ +');
                    }
                    
                    // Проверяем, что + только в начале (если есть)
                    if (substr_count($value, '+') > 1 || 
                        (strpos($value, '+') !== false && strpos($value, '+') !== 0)) {
                        $fail('Символ + может быть только в начале номера');
                    }
                    
                    // Удаляем все символы кроме цифр для проверки длины
                    $digitsOnly = preg_replace('/\D/', '', $value);
                    
                    if (strlen($digitsOnly) !== 11) {
                        $fail('Номер телефона должен содержать 11 цифр');
                    }
                },
            ],
            'position' => [
                'required',
                'integer',
                Rule::exists(Department::class, 'id')
            ],
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Поле "Имя" обязательно для заполнения',
            'email.required' => 'Поле "Email" обязательно для заполнения',
            'email.email' => 'Введите корректный email адрес',
            'email.max' => 'Email не должен превышать 255 символов',
            'phone.required' => 'Номер телефона является обязательным полем.',
            'phone.phone' => 'Не верный формат ввода номера телефона',
            'position.required' => 'Необходимо выбрать должность',
            'position.integer' => 'Некорректный формат должности',
            'position.exists' => 'Выбранная должность не существует',
            'resume.required' => 'Необходимо прикрепить резюме',
            'resume.file' => 'Резюме должно быть файлом',
            'resume.mimes' => 'Резюме должно быть в формате PDF, DOC или DOCX',
            'resume.max' => 'Размер файла резюме не должен превышать 2MB',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'name' => trim($this->name),
            'email' => strtolower(trim($this->email)),
            'phone' => preg_replace('/[^0-9+]/', '', $this->phone),
        ]);
    }
}