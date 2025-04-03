<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
                $messages = $this->getMessages($user->id, $currentInterlocutor->id);
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
        ]);
        
        if (!$request->message && !$request->hasFile('attachment')) {
            return back()->with('error', 'Введите сообщение или прикрепите файл');
        }
        
        try {
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filePath = $file->store('chat_attachments', 'public');
                
                Message::create([
                    'sender_id' => Auth::id(),
                    'receiver_id' => $request->receiver_id,
                    'message' => $request->message,
                    'document' => $filePath,
                    'original_filename' => $file->getClientOriginalName(),
                    'reply_to' => $request->reply_to
                ]);
            }
            
            return back()->with('success', 'Сообщение отправлено');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при отправке сообщения: ' . $e->getMessage());
        }
    }
    
    private function getInterlocutors()
    {
        $user = Auth::user();
        $taskStatus = TaskStatus::where('user_id', $user->id)
            ->with(['hr_manager', 'tutor'])
            ->latest()
            ->first();
        
        $interlocutors = [];
        
        if ($taskStatus) {
            if ($taskStatus->hr_manager) {
                $interlocutors[] = $taskStatus->hr_manager;
            }
            
            if ($taskStatus->tutor) {
                $interlocutors[] = $taskStatus->tutor;
            }
        }
        
        return $interlocutors;
    }
    
    private function getMessages($userId, $interlocutorId)
    {
        return Message::where(function($query) use ($userId, $interlocutorId) {
                $query->where('sender_id', $userId)
                    ->where('receiver_id', $interlocutorId);
            })
            ->orWhere(function($query) use ($userId, $interlocutorId) {
                $query->where('sender_id', $interlocutorId)
                    ->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();
    }
}