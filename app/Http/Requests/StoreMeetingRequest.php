<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Call;
use Carbon\Carbon;

class StoreMeetingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $user = auth()->user();
        $isTutor = $user->role->name === \App\Enums\UserRoleEnum::TUTOR_WORKER->value;
        
        $rules = [
            'user_id' => 'required|exists:users,id',
            'date' => [
                'required',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) {
                    if (Carbon::parse($value)->isWeekend()) {
                        $fail('Созвоны нельзя назначать на выходные дни.');
                    }
                }
            ],
            'time' => 'required',
            'link' => 'required|url',
            'type' => [
                'required',
                'in:primary,technical,final',
                function ($attribute, $value, $fail) use ($isTutor, $user) {
                    if ($isTutor && $value !== 'technical') {
                        $fail('Тьютор может создавать только технические созвоны.');
                    }
                }
            ],
        ];

        if (!$isTutor) {
            $rules['tutor_id'] = 'required|exists:users,id';
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$validator->errors()->any()) {
                $this->validateMeetingTime($validator);
            }
        });
    }

    protected function validateMeetingTime($validator)
    {
        $existingMeeting = Call::where('date', $this->date)
            ->where('time', $this->time)
            ->when($this->route('meeting'), function ($query) {
                $query->where('id', '!=', $this->route('meeting')->id);
            })
            ->exists();

        if ($existingMeeting) {
            $validator->errors()->add('time', 'На это время уже назначен другой созвон.');
        }
    }

    public function messages()
    {
        return [
            'time.required' => 'Поле время обязательно для заполнения.',
            'date.after_or_equal' => 'Дата созвона не может быть в прошлом.',
            'tutor_id.required' => 'Необходимо выбрать тьютора.',
        ];
    }
}