<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnum;
use App\Models\Message;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'answer_message_id' => 'nullable|integer|exists:messages,id' // Изменено с reply_to
        ]);

        if (!$request->message && !$request->hasFile('attachment')) {
            return back()->with('error', 'Введите сообщение или прикрепите файл');
        }

        try {
            $messageData = [
                'sender_id' => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'message' => $request->message,
                'answer_message_id' => $request->answer_message_id // Изменено с reply_to
            ];

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

            Message::create($messageData);

            return back()->with('success', 'Сообщение отправлено');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при отправке сообщения: ' . $e->getMessage());
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
        $interlocutors = collect();

        // Получаем всех кандидатов, связанных с текущим пользователем
        $query = TaskStatus::query();

        if ($user->role->name === UserRoleEnum::SUPER_ADMIN->value) {
            $query->where('tutor_id', $user->id);
        } elseif ($user->role->name === UserRoleEnum::ADMIN->value) {
            $query->where('hr_manager_id', $user->id);
        }

        $candidates = $query->with(['user'])
            ->get()
            ->map(function ($taskStatus) {
                if ($taskStatus->user) {
                    $taskStatus->user->status = $taskStatus->status;
                    $taskStatus->user->position = 'Кандидат';
                    return $taskStatus->user;
                }
                return null;
            })
            ->filter();

        // Получаем всех HR-менеджеров (для тьюторов) или всех тьюторов (для HR)
        $colleagues = TaskStatus::query()
            ->when($user->role->name === UserRoleEnum::SUPER_ADMIN->value, function ($q) {
                return $q->whereNotNull('hr_manager_id');
            })
            ->when($user->role->name === UserRoleEnum::ADMIN->value, function ($q) {
                return $q->whereNotNull('tutor_id');
            })
            ->with($user->role->name === UserRoleEnum::SUPER_ADMIN->value ? 'hr_manager' : 'tutor')
            ->get()
            ->pluck($user->role->name === UserRoleEnum::SUPER_ADMIN->value ? 'hr_manager' : 'tutor')
            ->unique()
            ->filter()
            ->map(function ($colleague) use ($user) {
                $colleague->position = $user->role->name === UserRoleEnum::SUPER_ADMIN->value
                    ? 'HR-менеджер'
                    : 'Тьютор';
                return $colleague;
            });

        return $candidates->merge($colleagues)->unique('id');
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