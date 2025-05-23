<?php

namespace App\Http\Controllers;

use App\Actions\TaskStatusAction;
use App\Models\Task;
use App\Models\User;
use App\Models\Report;
use App\Models\TaskStatus;
use App\Enums\TaskStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Mail\TaskStatusMail;
use Illuminate\Support\Facades\Mail;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;

class TaskController extends Controller
{
    // Отображение страницы с задачами
    public function index()
    {
        return view('workers.tasks');
    }

    // Получение списка задач с фильтрацией и пагинацией
    public function getTasks(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $search = $request->input('search', '');
        $status = $request->input('status', '');

        $query = TaskStatus::with(['user', 'task', 'tutor', 'hr_manager'])
            ->when($search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('task', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc');

        $tasks = $query->paginate($perPage);

        $formattedTasks = $tasks->map(function ($taskStatus) {
            return [
                'id' => $taskStatus->user->id,
                'task_status_id' => $taskStatus->id,
                'name' => $taskStatus->user->name,
                'avatar' => $taskStatus->user->avatar_url,
                'tutor' => $taskStatus->tutor ? [
                    'id' => $taskStatus->tutor->id,
                    'name' => $taskStatus->tutor->name,
                    'avatar' => $taskStatus->tutor->avatar_url,
                ] : null,
                'hr_manager' => $taskStatus->hr_manager ? [
                    'id' => $taskStatus->hr_manager->id,
                    'name' => $taskStatus->hr_manager->name,
                    'avatar' => $taskStatus->hr_manager->avatar_url,
                ] : null,
                'task' => [
                    'id' => $taskStatus->task->id,
                    'title' => $taskStatus->task->title,
                    'difficulty' => $taskStatus->task->level,
                    'deadline' => $taskStatus->end_date?->format('d.m.Y'),
                    'github' => $taskStatus->github_repo,
                    'status' => $taskStatus->status,
                    'document' => $taskStatus->task->task
                        ? Storage::url($taskStatus->task->task)
                        : null,
                ]
            ];
        });

        return response()->json([
            'data' => $formattedTasks,
            'current_page' => $tasks->currentPage(),
            'per_page' => $tasks->perPage(),
            'total' => $tasks->total(),
            'last_page' => $tasks->lastPage(),
        ]);
    }

    public function getTaskStatus($taskStatusId)
    {
        $taskStatus = TaskStatus::with(['user', 'task'])->findOrFail($taskStatusId);

        return response()->json([
            'user_id' => $taskStatus->user_id,
            'task_id' => $taskStatus->task_id,
            'status' => $taskStatus->status,
        ]);
    }

    // Получение информации о кандидате
    public function getCandidateInfo($id)
    {
        $candidate = User::findOrFail($id);

        return response()->json([
            'name' => $candidate->name,
            'email' => $candidate->email,
            'phone' => $candidate->phone,
            'telegram' => $candidate->telegram_user_id
                ? $candidate->telegramUser->username
                : null,
        ]);
    }

    // Добавим новый метод для получения информации о тьюторе
    public function getTutorInfo($id)
    {
        $tutor = User::with('worker')->findOrFail($id);

        return response()->json([
            'name' => $tutor->name,
            'email' => $tutor->email,
            'phone' => $tutor->phone,
            'telegram' => $tutor->telegram_user_id
                ? $tutor->telegramUser->username
                : null,
            'post' => $tutor->worker->post->name ?? null,
            'department' => $tutor->worker->department->name ?? null,
            'level' => $tutor->worker->level_of_experience ?? null,
            'hire_date' => $tutor->worker->hire_date ?? null,
        ]);
    }

    // Добавим новый метод для получения информации о HR-менеджере
    public function getHrManagerInfo($id)
    {
        $hrManager = User::findOrFail($id);

        return response()->json([
            'name' => $hrManager->name,
            'email' => $hrManager->email,
            'phone' => $hrManager->phone,
            'telegram' => $hrManager->telegram_user_id
                ? $hrManager->telegramUser->username
                : null,
            'post' => $hrManager->worker->post->name ?? null,
            'department' => $hrManager->worker->department->name ?? null,
            'level' => $hrManager->worker->level_of_experience ?? null,
            'hire_date' => $hrManager->worker->hire_date ?? null,
        ]);
    }

    // Получение информации о задании
    public function getTaskInfo($taskStatusId)
    {
        $taskStatus = TaskStatus::with(['user', 'task'])
            ->findOrFail($taskStatusId);

        return response()->json([
            'title' => $taskStatus->task->title,
            'difficulty' => $taskStatus->task->level,
            'document' => $taskStatus->task->task
                ? Storage::url($taskStatus->task->task)
                : null,
        ]);
    }

    // Обновление статуса задания
    public function updateStatus($taskStatusId, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:' . implode(',', TaskStatusEnum::getAll()),
            'comment' => 'nullable|string|max:1000',
            'report' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $taskStatus = TaskStatus::findOrFail($taskStatusId);
        $user = User::with('telegramUser')->findOrFail($taskStatus->user_id);

        $filePath = null;
        if ($request->hasFile('report') && $request->file('report')->isValid()) {
            try {
                $file = $request->file('report');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('moonshine_reports', $fileName, 'public');
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка при загрузке файла: ' . $e->getMessage()
                ], 500);
            }
        }

        $taskStatus->update([
            'status' => $validated['status'],
            'comment' => $validated['comment'] ?? null,
            'tutor_id' => auth()->id(),
        ]);

        if (in_array($validated['status'], [TaskStatusEnum::APPROVED->value, TaskStatusEnum::FAILED->value, TaskStatusEnum::REVISION->value]) && $filePath) {
            Report::create([
                'user_id' => $user->id,
                'task_id' => $taskStatus->id,
                'tutor_id' => auth()->id(),
                'report' => $filePath,
            ]);
        }

        // Отправка email уведомления
        try {
            Mail::to($user->email)->send(
                new TaskStatusMail(
                    $user,
                    $this->getStatusType($validated['status']),
                    $taskStatus->id,
                    $validated['comment'] ?? null,
                    $filePath
                )
            );
        } catch (\Exception $e) {
            \Log::error('Ошибка отправки email: ' . $e->getMessage());
        }

        // Отправка Telegram уведомления
        $this->sendTelegramTaskStatusNotification(
            $user,
            $taskStatus,
            $validated['status'],
            $validated['comment'] ?? null,
            $filePath
        );

        return response()->json([
            'success' => true,
            'message' => $this->getStatusMessage($validated['status']),
        ]);
    }

    // Отправка уведомления в Telegram
    protected function sendTelegramTaskStatusNotification($user, $taskStatus, $status, $comment, $filePath = null)
    {
        // Проверяем, есть ли у пользователя привязанный Telegram аккаунт
        if (!$user->telegram_user_id || !$user->telegramUser) {
            return;
        }

        try {
            $telegram = new Api(config('telegram.bot_token'));
            $siteUrl = env('WEBHOOK_URL', 'https://your-default-site.com');

            $statusMessages = [
                TaskStatusEnum::APPROVED->value => '✅ Задание одобрено',
                TaskStatusEnum::FAILED->value => '❌ Задание не принято',
                TaskStatusEnum::REVISION->value => '🔄 Требуется доработка',
            ];

            $text = "📢 <b>Статус задания изменен</b>\n\n";
            $text .= "📌 <b>Задание:</b> {$taskStatus->task->title}\n";
            $text .= "📝 <b>Статус:</b> {$statusMessages[$status]}\n";

            if ($comment) {
                $text .= "💬 <b>Комментарий:</b>\n{$comment}\n";
            }

            $text .= "\n🔗 <a href='{$siteUrl}'>Вернуться на сайт</a>";

            // Если есть файл отчета
            if ($filePath) {
                $fullPath = storage_path('app/public/' . $filePath);
                $inputFile = InputFile::create($fullPath);

                $telegram->sendDocument([
                    'chat_id' => $user->telegramUser->telegram_id,
                    'document' => $inputFile,
                    'caption' => $text,
                    'parse_mode' => 'HTML'
                ]);
            } else {
                $telegram->sendMessage([
                    'chat_id' => $user->telegramUser->telegram_id,
                    'text' => $text,
                    'parse_mode' => 'HTML'
                ]);
            }

        } catch (\Exception $e) {
            \Log::error("Ошибка отправки уведомления в Telegram: " . $e->getMessage());
        }
    }

    // Вспомогательные методы для преобразования статусов
    protected function getStatusType(string $status): string
    {
        return match ($status) {
            TaskStatusEnum::REVISION->value => 'revision',
            TaskStatusEnum::APPROVED->value => 'approved',
            TaskStatusEnum::FAILED->value => 'failed',
            default => 'status_changed'
        };
    }

    protected function getStatusMessage(string $status): string
    {
        return match ($status) {
            TaskStatusEnum::REVISION->value => 'Задание отправлено на доработку',
            TaskStatusEnum::APPROVED->value => 'Задание одобрено',
            TaskStatusEnum::FAILED->value => 'Задание провалено',
            default => 'Статус задания изменен'
        };
    }

    // Добавить в TaskController
    public function adopted($taskStatusId)
    {
        try {
            $action = new TaskStatusAction();
            $result = $action->adopted($taskStatusId);
            return response()->json(['success' => true, 'message' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function failed($taskStatusId, Request $request)
    {
        try {
            $action = new TaskStatusAction();
            $result = $action->deny($taskStatusId);
            return response()->json(['success' => true, 'message' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function technicalCall($taskStatusId, Request $request)
    {
        try {
            $action = new TaskStatusAction();
            $result = $action->technical_call([
                'id' => $taskStatusId,
                'meeting_link' => $request->input('meeting_link'),
                'date' => $request->input('date'),
                'time' => $request->input('time'),
            ]);
            return response()->json(['success' => true, 'message' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function finalCall($taskStatusId, Request $request)
    {
        try {
            $action = new TaskStatusAction();
            $result = $action->final_call([
                'id' => $taskStatusId,
                'meeting_link' => $request->input('meeting_link'),
                'date' => $request->input('date'),
                'time' => $request->input('time'),
            ]);
            return response()->json(['success' => true, 'message' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Получение списка доступных статусов
    public function getStatuses()
    {
        return response()->json(TaskStatusEnum::changeStatus());
    }
}
