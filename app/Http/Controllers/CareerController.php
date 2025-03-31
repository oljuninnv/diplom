<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatusEnum;
use App\Enums\UserRoleEnum;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Role;
use App\Models\Application;
use App\Models\Vacancy;
use App\Models\Department;
use Illuminate\Support\Facades\Mail;
use App\Mail\CareerApplication;
use App\Mail\UserApplicationConfirmation;
use App\Http\Requests\CareerApplicationRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
    public function submitApplication(CareerApplicationRequest $request)
    {
        // Валидация данных с кастомными сообщениями об ошибках
        $validatedData = $request->validated();

        try {
            // Сохраняем файл с оригинальным именем
            $resumeFile = $request->file('resume');
            $resumePath = $resumeFile->storeAs(
                'moonshine_applications',
                time() . '_' . Str::slug($validatedData['name']) . '.' . $resumeFile->extension(),
                'public'
            );

            // Получаем или создаем роль пользователя
            $userRole = Role::firstOrCreate(
                ['name' => UserRoleEnum::USER->value],
                ['description' => 'Standard application user']
            );

            // Создаем пользователя
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'role_id' => $userRole->id,
                'email_verified_at' => now(), // Помечаем email как подтвержденный
            ]);

            // Создаем заявку
            Application::create([
                'user_id' => $user->id,
                'resume' => $resumePath,
                'status' => ApplicationStatusEnum::PENDING->value,
                'department_id' => $validatedData['position'],
            ]);

            $mailData = array_merge($validatedData, [
                'resume_path' => $resumePath,
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'position' => Department::find($validatedData['position'])->name ?? $validatedData['position'],
            ]);

            // Отправка писем с обработкой возможных ошибок
            try {
                Mail::send(new CareerApplication($mailData));
                Mail::to($validatedData['email'])->send(new UserApplicationConfirmation($mailData));
            } catch (\Exception $mailException) {
                Log::error('Ошибка отправки email: ' . $mailException->getMessage());
            }

            return redirect()->route('career')
                ->with('success', 'Заявка успешно отправлена! Проверьте ваш email для подтверждения.');

        } catch (\Exception $e) {

            Log::error('Application Error: ' . $e->getMessage());
            Log::error('Stack Trace: ' . $e->getTraceAsString());

            if (isset($resumePath)) {
                Storage::disk('public')->delete($resumePath);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Произошла ошибка: ' . $e->getMessage());
        }
    }
}