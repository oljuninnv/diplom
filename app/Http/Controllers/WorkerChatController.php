<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnum;
use App\Models\Message;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Api;
use App\Models\TelegramUser;
use Telegram\Bot\FileUpload\InputFile;

class WorkerChatController extends Controller
{
    public function index($interlocutorId = null)
    {
        $user = Auth::user();

        // Получаем собеседников (кандидатов)
        $interlocutors = $this->getInterlocutors();

        \Log::info($interlocutors);

        // Если собеседник не указан, берем первого из списка
        if (!$interlocutorId && count($interlocutors)) {
            $interlocutorId = $interlocutors[0]->id;
        }

        $messages = [];
        $currentInterlocutor = null;

        if ($interlocutorId) {
            // Находим текущего собеседника
            $currentInterlocutor = collect($interlocutors)->first(function ($item) use ($interlocutorId) {
                return $item->id == $interlocutorId;
            });

            if ($currentInterlocutor) {
                // Получаем сообщения и помечаем как прочитанные
                $messages = $this->getMessages($user->id, $currentInterlocutor->id);

                // Помечаем сообщения как прочитанные
                Message::where('receiver_id', $user->id)
                    ->where('sender_id', $currentInterlocutor->id)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            }
        }

        return view('workers.chat', [
            'interlocutors' => $interlocutors,
            'messages' => $messages,
            'currentInterlocutor' => $currentInterlocutor
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'message' => 'required_without:attachment|string|max:1000',
            'attachment' => 'nullable|file|max:10240',
            'answer_message_id' => 'nullable|integer|exists:messages,id'
        ]);

        if (!$request->message && !$request->hasFile('attachment')) {
            return back()->with('error', 'Введите сообщение или прикрепите файл');
        }

        try {
            $messageData = [
                'sender_id' => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'message' => $request->message,
                'answer_message_id' => $request->answer_message_id
            ];

            $filePath = null;
            $originalName = null;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = pathinfo($originalName, PATHINFO_FILENAME);
                $uniqueName = $fileName . '_' . time() . '.' . $extension;
                $filePath = $file->storeAs('chat_attachments', $uniqueName, 'public');

                $messageData['document'] = $filePath;
                $messageData['original_filename'] = $originalName;
            }

            $message = Message::create($messageData);

            // Отправка уведомления в Telegram
            $receiver = User::find($request->receiver_id);
            if ($receiver && $receiver->telegram_user_id) {
                $telegramUser = TelegramUser::find($receiver->telegram_user_id);
                if ($telegramUser) {
                    $this->sendTelegramNotification(
                        $telegramUser->telegram_id,
                        $message,
                        $filePath,
                        $originalName
                    );
                }
            }

            return back()->with('success', 'Сообщение отправлено');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при отправке сообщения: ' . $e->getMessage());
        }
    }

    protected function sendTelegramNotification($telegramId, $message, $filePath = null, $originalFilename = null)
    {
        try {
            $sender = User::find($message->sender_id);
            $siteUrl = env('WEBHOOK_URL', 'https://your-default-site.com');

            $text = "🔔 У вас новое сообщение от {$sender->name}!\n\n";
            $text .= "💬 Текст: {$message->message}\n\n";
            $text .= "📎 Ссылка на чат: {$siteUrl}";

            $telegram = new Api(config('telegram.bot_token'));

            // Если есть вложение
            if ($filePath) {
                $fullPath = storage_path('app/public/' . $filePath);
                $inputFile = InputFile::create($fullPath, $originalFilename);

                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];

                if (in_array($extension, $imageExtensions)) {
                    $telegram->sendPhoto([
                        'chat_id' => $telegramId,
                        'photo' => $inputFile,
                        'caption' => $text
                    ]);
                } elseif (in_array($extension, $documentExtensions)) {
                    $telegram->sendDocument([
                        'chat_id' => $telegramId,
                        'document' => $inputFile,
                        'caption' => $text
                    ]);
                } else {
                    // Для других типов файлов просто отправляем ссылку
                    $fileUrl = asset('storage/' . $filePath);
                    $text .= "\n\n📁 Файл: {$fileUrl}";
                    $telegram->sendMessage([
                        'chat_id' => $telegramId,
                        'text' => $text
                    ]);
                }
            } else {
                $telegram->sendMessage([
                    'chat_id' => $telegramId,
                    'text' => $text,
                    'parse_mode' => 'HTML'
                ]);
            }

        } catch (\Exception $e) {
            \Log::error("Ошибка отправки уведомления в Telegram: " . $e->getMessage());
        }
    }

    public function deleteMessage($messageId)
    {
        \Log::info('Delete message attempt', [
            'user_id' => Auth::id(),
            'message_id' => $messageId
        ]);

        $message = Message::findOrFail($messageId);

        if ($message->sender_id != Auth::id()) {
            abort(403, 'Вы можете удалять только свои сообщения');
        }

        if ($message->document) {
            Storage::disk('public')->delete($message->document);
        }

        $message->delete();

        return back()->with('success', 'Сообщение удалено');
    }

    private function getInterlocutors()
    {
        $user = Auth::user();
        $interlocutors = collect();

        // Получаем всех кандидатов, связанных с текущим пользователем
        $query = TaskStatus::query();

        if (in_array($user->role->name, [UserRoleEnum::ADMIN->value, UserRoleEnum::SUPER_ADMIN->value])) {
            // Для HR-менеджеров (ADMIN и SUPER_ADMIN) получаем их кандидатов
            $query->where('hr_manager_id', $user->id);
        } elseif ($user->role->name === UserRoleEnum::TUTOR_WORKER->value) {
            // Для тьюторов получаем их кандидатов
            $query->where('tutor_id', $user->id);
        }

        $candidates = $query->with(['user', 'task'])
            ->get()
            ->map(function ($taskStatus) {
                if ($taskStatus->user) {
                    $taskStatus->user->status = $taskStatus->status;
                    $taskStatus->user->position = $taskStatus->post ? $taskStatus->post->name : 'Кандидат';
                    $taskStatus->user->task_status = $taskStatus->status;
                    return $taskStatus->user;
                }
                return null;
            })
            ->filter();

        // Получаем коллег (HR для тьюторов и тьюторов для HR)
        $colleagues = TaskStatus::query()
            ->when(
                in_array($user->role->name, [UserRoleEnum::ADMIN->value, UserRoleEnum::SUPER_ADMIN->value]),
                function ($q) {
                    // Для HR-менеджеров получаем тьюторов
                    return $q->whereNotNull('tutor_id');
                },
                function ($q) {
                    // Для тьюторов получаем HR-менеджеров
                    return $q->whereNotNull('hr_manager_id');
                }
            )
            ->with($user->role->name === UserRoleEnum::TUTOR_WORKER->value ? 'hr_manager' : 'tutor')
            ->get()
            ->pluck($user->role->name === UserRoleEnum::TUTOR_WORKER->value ? 'hr_manager' : 'tutor')
            ->unique()
            ->filter()
            ->map(function ($colleague) use ($user) {
                if ($colleague && $colleague->id != $user->id) { // Исключаем текущего пользователя
                    $colleague->position = $user->role->name === UserRoleEnum::TUTOR_WORKER->value
                        ? 'HR-менеджер'
                        : 'Тьютор';
                    return $colleague;
                }
                return null;
            })
            ->filter();

        return $candidates->merge($colleagues)->unique('id')->values();
    }

    private function getMessages($userId, $interlocutorId)
    {
        return Message::where(function ($query) use ($userId, $interlocutorId) {
            $query->where('sender_id', $userId)
                ->where('receiver_id', $interlocutorId);
        })
            ->orWhere(function ($query) use ($userId, $interlocutorId) {
                $query->where('sender_id', $interlocutorId)
                    ->where('receiver_id', $userId);
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();
    }
}