<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatusEnum;
use Illuminate\Support\Facades\Mail;
use App\Models\TaskStatus;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Mail\TaskSubmittedNotification;
use App\Mail\TaskConfirmation;

class CandidateTaskController extends Controller
{
    public function show()
    {
        // Получаем последнее задание для авторизованного пользователя
        $taskStatus = TaskStatus::where('user_id', Auth::id())
            ->with('task')
            ->latest()
            ->first();

        if (!$taskStatus || !$taskStatus->task) {
            abort(404, 'Задание не найдено');
        }

        // Получаем путь к файлу задания
        $filePath = $taskStatus->task->task;
        $fileUrl = Storage::disk('public')->url($filePath);

        $canEdit = !in_array($taskStatus->status, [
            TaskStatusEnum::UNDER_REVIEW->value,
            TaskStatusEnum::APPROVED->value,
        ]);

        return view('users.task', [
            'taskStatus' => $taskStatus,
            'fileUrl' => $fileUrl,
            'canEdit' => $canEdit,
        ]);
    }

    public function submit(Request $request)
    {
        // Находим последнее задание пользователя
        $taskStatus = TaskStatus::where('user_id', Auth::id())
            ->with(['task', 'user'])
            ->latest()
            ->firstOrFail();

        // Проверяем, можно ли редактировать
        if (
            in_array($taskStatus->status, [
                TaskStatusEnum::UNDER_REVIEW->value,
                TaskStatusEnum::APPROVED->value
            ])
        ) {
            return redirect()->route('task')
                ->with('error', 'Вы уже отправили решение на проверку и не можете его изменить');
        }

        $validated = $request->validate([
            'github_repo' => 'required|url|regex:/^https:\/\/github\.com\/[a-zA-Z0-9_-]+\/[a-zA-Z0-9_-]+$/',
        ]);

        // Обновляем статус и ссылку на репозиторий
        $taskStatus->update([
            'github_repo' => $validated['github_repo'],
            'status' => TaskStatusEnum::UNDER_REVIEW->value,
        ]);

        // Проверяем наличие файла задания
        $taskFileExists = Storage::disk('public')
            ->exists($taskStatus->task->task);

        // Отправка писем с обработкой возможных ошибок
        try {
            Mail::send(new TaskSubmittedNotification($taskStatus, $taskStatus->user));

            Mail::to($taskStatus->user->email)
                ->send(new TaskConfirmation($taskStatus->user, $taskStatus));

        } catch (\Exception $mailException) {
            \Log::error('Ошибка отправки email: ' . $mailException->getMessage());

            // Можно добавить дополнительное логирование
            if (!$taskFileExists) {
                \Log::warning('Файл задания не найден: ' . $taskStatus->task->task);
            }
        }

        return redirect()->route('task')
            ->with('success', 'Решение успешно отправлено на проверку!');
    }
}