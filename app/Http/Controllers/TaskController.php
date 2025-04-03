<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Report;
use App\Models\TaskStatus;
use App\Enums\TaskStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        $query = TaskStatus::with(['user', 'task'])
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
                'task_status_id' => $taskStatus->id, // Добавлено
                'name' => $taskStatus->user->name,
                'avatar' => $taskStatus->user->avatar_url,
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
        // Другие необходимые поля
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
        ]);

        $taskStatus = TaskStatus::findOrFail($taskStatusId);
        $taskStatus->update([
            'status' => $validated['status'],
            'tutor_id' => auth()->id(),
        ]);

        return response()->json(['success' => true]);
    }

    // Создание отчета по заданию
    public function createReport($taskStatusId, Request $request)
    {
        $request->validate([
            'report' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,xls,xlsx',
                'max:2048',
                function ($attribute, $value, $fail) {
                    if (!$value->isValid()) {
                        $fail('Файл не был успешно загружен.');
                    }
                },
            ],
        ]);

        try {
            $file = $request->file('report');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('moonshine_reports', $fileName, 'public');

            $taskStatus = TaskStatus::findOrFail($taskStatusId);

            Report::create([
                'user_id' => $taskStatus->user_id,
                'task_id' => $taskStatus->id,
                'tutor_id' => auth()->id(),
                'report' => $filePath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Отчёт успешно создан',
                'path' => Storage::url($filePath)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка сервера: ' . $e->getMessage()
            ], 500);
        }
    }

    // Получение списка доступных статусов
    public function getStatuses()
    {
        return response()->json(TaskStatusEnum::changeStatus());
    }
}