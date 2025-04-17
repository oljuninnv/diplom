<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Api;
use App\Models\TelegramUser;
use Telegram\Bot\FileUpload\InputFile;

class ChatController extends Controller
{
    public function index($interlocutorId = null)
    {
        $user = Auth::user();

        // Получаем собеседников (HR и тьютора)
        $interlocutors = $this->getInterlocutors();

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

        return view('users.chat', [
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
            $this->sendTelegramNotification($request->receiver_id, $message, $filePath, $originalName);

            return back()->with('success', 'Сообщение отправлено');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при отправке сообщения: ' . $e->getMessage());
        }
    }

    protected function sendTelegramNotification($receiverId, $message, $filePath = null, $originalFilename = null)
    {
        try {
            $receiver = User::with('telegramUser')->find($receiverId);

            // Проверяем, есть ли у получателя привязанный аккаунт Telegram
            if (!$receiver || !$receiver->telegram_user_id || !$receiver->telegramUser) {
                return;
            }

            $sender = User::find($message->sender_id);
            $siteUrl = env('WEBHOOK_URL', 'https://your-default-site.com');

            $text = "🔔 У вас новое сообщение в чате!\n\n";
            $text .= "👤 От: {$sender->name}\n";
            $text .= "📝 Текст: {$message->message}\n\n";
            $text .= "🔗 Перейти в чат: {$siteUrl}";

            $telegram = new Api(config('telegram.bot_token'));

            // Если есть вложение
            if ($filePath) {
                $fullPath = storage_path('app/public/' . $filePath);

                try {
                    $inputFile = InputFile::create($fullPath, $originalFilename);

                    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                    $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];

                    if (in_array($extension, $imageExtensions)) {
                        $telegram->sendPhoto([
                            'chat_id' => $receiver->telegramUser->telegram_id,
                            'photo' => $inputFile,
                            'caption' => $text,
                            'parse_mode' => 'HTML'
                        ]);
                    } elseif (in_array($extension, $documentExtensions)) {
                        $telegram->sendDocument([
                            'chat_id' => $receiver->telegramUser->telegram_id,
                            'document' => $inputFile,
                            'caption' => $text,
                            'parse_mode' => 'HTML'
                        ]);
                    } else {
                        // Для других типов файлов просто отправляем ссылку
                        $fileUrl = asset('storage/' . $filePath);
                        $text .= "\n\n📁 Вложение: {$fileUrl}";
                        $telegram->sendMessage([
                            'chat_id' => $receiver->telegramUser->telegram_id,
                            'text' => $text,
                            'parse_mode' => 'HTML'
                        ]);
                    }
                } catch (\Exception $fileException) {
                    \Log::error("Ошибка отправки файла в Telegram: " . $fileException->getMessage());
                    // Если не удалось отправить файл, отправляем просто текст с ссылкой на файл
                    $fileUrl = asset('storage/' . $filePath);
                    $text .= "\n\n📁 Вложение: {$fileUrl}";
                    $telegram->sendMessage([
                        'chat_id' => $receiver->telegramUser->telegram_id,
                        'text' => $text,
                        'parse_mode' => 'HTML'
                    ]);
                }
            } else {
                // Отправка только текстового сообщения
                $telegram->sendMessage([
                    'chat_id' => $receiver->telegramUser->telegram_id,
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
        $taskStatus = TaskStatus::where('user_id', $user->id)
            ->with(['hr_manager', 'tutor'])
            ->latest()
            ->first();

        $interlocutors = collect();

        if ($taskStatus) {
            // Добавляем HR-менеджера, если он есть и это не сам пользователь
            if ($taskStatus->hr_manager && $taskStatus->hr_manager->id != $user->id) {
                $taskStatus->hr_manager->position = 'HR-менеджер';
                $interlocutors->push($taskStatus->hr_manager);
            }

            // Добавляем тьютора, если он есть, это не сам пользователь и это не тот же человек, что и HR
            if ($taskStatus->tutor && $taskStatus->tutor->id != $user->id) {
                // Проверяем, не является ли тьютор тем же человеком, что и HR (но с другой ролью)
                if (!$taskStatus->hr_manager || $taskStatus->tutor->id != $taskStatus->hr_manager->id) {
                    $taskStatus->tutor->position = 'Тьютор';
                    $interlocutors->push($taskStatus->tutor);
                }
            }
        }

        // Удаляем дубликаты по id и сбрасываем ключи
        return $interlocutors->unique('id')->values()->all();
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