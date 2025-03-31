<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Vacancy;
use App\Models\Department;
use Illuminate\Support\Facades\Mail;
use App\Mail\CareerApplication;
use App\Mail\UserApplicationConfirmation;

class CareerController extends Controller
{
    /**
     * Отображает страницу с вакансиями
     */
    public function index()
    {
        // Получаем активные вакансии из базы данных
        $vacancies = Vacancy::get();

        // Получаем типы работ (отделы)
        $departments = Department::all();

        return view('career', [
            'vacancies' => $vacancies,
            'departments' => $departments
        ]);
    }

    /**
     * Обрабатывает отправку формы заявки
     */
    public function submitApplication(Request $request)
    {
        // Валидация данных
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:255',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        // Сохраняем файл
        $resumePath = $request->file('resume')->store('resumes');
        $validatedData['resume_path'] = $resumePath;

        try {
            // Отправляем письмо администратору
            Mail::send(new CareerApplication($validatedData));

            // Отправляем письмо-подтверждение пользователю
            Mail::to($validatedData['email'])->send(new UserApplicationConfirmation($validatedData));

            return redirect()->route('career')
                ->with('success', 'Ваша заявка успешно отправлена! На ваш email было отправлено подтверждение.');
        } catch (\Exception $e) {
            Log::error('Ошибка отправки письма: ' . $e->getMessage());

            return redirect()->route('career')
                ->with('error', 'Произошла ошибка при отправке заявки. Пожалуйста, попробуйте позже.');
        }
    }
}