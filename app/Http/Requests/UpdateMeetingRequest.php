<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Call;
use Carbon\Carbon;

class UpdateMeetingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $user = auth()->user();
        $isTutor = $user->isTutorWorker();
        $isAdmin = $user->isAdmin();
        
        return [
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
                function ($attribute, $value, $fail) use ($isTutor) {
                    if ($isTutor && $value !== 'technical') {
                        $fail('Тьютор может создавать только технические созвоны.');
                    }
                }
            ],
            'tutor_id' => $isTutor ? [] : ($isAdmin ? 'nullable' : 'required|exists:users,id'),
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isEmpty()) {
                $this->validateMeetingTime($validator);
            }
        });
    }

    protected function validateMeetingTime($validator)
    {
        $query = Call::where('date', $this->date)
            ->where('time', $this->time);

        if ($this->route('meeting')) {
            $query->where('id', '!=', $this->route('meeting')->id);
        }

        if ($query->exists()) {
            $validator->errors()->add('time', 'На это время уже назначен другой созвон.');
        }
    }

    public function messages()
    {
        return [
            'time.required' => 'Поле время обязательно для заполнения.',
            'date.after_or_equal' => 'Дата созвона не может быть в прошлом.',
            'tutor_id.required' => 'Поле тьютор обязательно для заполнения.',
            'tutor_id.exists' => 'Выбранный тьютор не существует.',
        ];
    }
}