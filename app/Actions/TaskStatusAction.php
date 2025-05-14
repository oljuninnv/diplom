<?php

namespace App\Actions;

use App\Enums\TaskStatusEnum;
use App\Enums\CallEnum;
use App\Models\Call;
use App\Models\Report;
use App\Models\TaskStatus;
use App\Models\User;
use App\Mail\CallMail;
use App\Mail\TaskStatusMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;
use Illuminate\Support\Facades\Log;

class TaskStatusAction
{
    public function revision(array $params)
    {
        $task = TaskStatus::findOrFail($params['id']);
        $task->update(['status' => TaskStatusEnum::REVISION->value]);

        $user = User::with('telegramUser')->findOrFail($task->user_id);

        // Отправка email с обработкой ошибок
        $this->sendEmailNotification(
            $user->email,
            new TaskStatusMail(
                $user,
                'revision',
                $task->id,
                $params['comment'] ?? null,
                $params['file_path'] ?? null
            )
        );

        // Отправка в Telegram
        $this->sendTelegramTaskStatusNotification(
            $user,
            $task,
            TaskStatusEnum::REVISION->value,
            $params['comment'] ?? null,
            $params['file_path'] ?? null
        );

        return 'Задание отправлено на доработку';
    }

    public function approved(array $params)
    {
        $task = TaskStatus::findOrFail($params['id']);
        $task->update(['status' => TaskStatusEnum::APPROVED->value]);

        $user = User::with('telegramUser')->findOrFail($task->user_id);

        Report::create([
            'tutor_id' => auth()->id(),
            'user_id' => $user->id,
            'task_id' => $task->id,
            'report' => $params['file_path']
        ]);

        // Отправка email с обработкой ошибок
        $this->sendEmailNotification(
            $user->email,
            new TaskStatusMail(
                $user,
                'approved',
                $task->id,
                null,
                $params['file_path'] ?? null
            )
        );

        // Отправка в Telegram
        $this->sendTelegramTaskStatusNotification(
            $user,
            $task,
            TaskStatusEnum::APPROVED->value,
            null,
            $params['file_path'] ?? null
        );

        return 'Задание одобрено';
    }

    public function failed(array $params)
    {
        $task = TaskStatus::findOrFail($params['id']);
        $task->update(['status' => TaskStatusEnum::FAILED->value]);

        $user = User::with('telegramUser')->findOrFail($task->user_id);

        Report::create([
            'tutor_id' => auth()->id(),
            'user_id' => $user->id,
            'task_id' => $task->id,
            'report' => $params['file_path']
        ]);

        // Отправка email с обработкой ошибок
        $this->sendEmailNotification(
            $user->email,
            new TaskStatusMail(
                $user,
                'failed',
                $task->id,
                $params['comment'] ?? null,
                $params['file_path'] ?? null
            )
        );

        // Отправка в Telegram
        $this->sendTelegramTaskStatusNotification(
            $user,
            $task,
            TaskStatusEnum::FAILED->value,
            $params['comment'] ?? null,
            $params['file_path'] ?? null
        );

        return 'Задание провалено';
    }

    public function final_call(array $params)
    {
        try {
            $taskStatus = TaskStatus::findOrFail($params['id']);
            $candidateId = $taskStatus['user_id'];

            // Проверяем есть ли у кандидата активные созвоны
            $existingCall = Call::where('candidate_id', $candidateId)
                ->where(function ($query) use ($params) {
                    $query->where('date', '>', now()->format('Y-m-d'))
                        ->orWhere(function ($q) use ($params) {
                            $q->where('date', now()->format('Y-m-d'))
                                ->where('time', '>=', now()->format('H:i:s'));
                        });
                })
                ->first();

            if ($existingCall) {
                throw new \Exception("У кандидата уже есть активный созвон (ID: {$existingCall->id}, тип: {$existingCall->type}, дата: {$existingCall->date}, время: {$existingCall->time})");
            }

            $call = Call::create([
                'type' => CallEnum::FINAL ->value,
                'meeting_link' => $params['meeting_link'],
                'date' => $params['date'],
                'time' => $params['time'],
                'candidate_id' => $candidateId,
                'tutor_id' => $taskStatus['tutor_id'],
                'hr_manager_id' => $taskStatus['hr_manager_id']
            ]);

            $tutor = User::with('telegramUser')->findOrFail($taskStatus['tutor_id']);
            $hrManager = User::with('telegramUser')->findOrFail($taskStatus['hr_manager_id']);
            $candidate = User::with('telegramUser')->findOrFail($candidateId);

            $emailData = [
                'candidateName' => $candidate->name,
                'tutorName' => $tutor->name,
                'hrManagerName' => $hrManager->name,
                'date' => $call->date,
                'time' => $call->time,
                'meetingLink' => $call->meeting_link,
                'companyName' => 'ATWINTA'
            ];

            // Отправка уведомлений
            $this->sendEmailNotification($candidate->email, new CallMail($emailData));
            $this->sendTelegramCallNotification($candidate, $call, 'final');

            $this->sendEmailNotification($tutor->email, new CallMail($emailData));
            $this->sendTelegramCallNotification($tutor, $call, 'final');

            $this->sendEmailNotification($hrManager->email, new CallMail($emailData));
            $this->sendTelegramCallNotification($hrManager, $call, 'final');

            return 'Финальный созвон назначен.';

        } catch (\Exception $e) {
            Log::error('Ошибка при назначении финального созвона', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function technical_call(array $params)
    {
        try {
            $taskStatus = TaskStatus::findOrFail($params['id']);
            $candidateId = $taskStatus['user_id'];

            // Проверяем есть ли у кандидата активные созвоны
            $existingCall = Call::where('candidate_id', $candidateId)
                ->where(function ($query) use ($params) {
                    $query->where('date', '>', now()->format('Y-m-d'))
                        ->orWhere(function ($q) use ($params) {
                            $q->where('date', now()->format('Y-m-d'))
                                ->where('time', '>=', now()->format('H:i:s'));
                        });
                })
                ->first();

            if ($existingCall) {
                throw new \Exception("У кандидата уже есть активный созвон (ID: {$existingCall->id}, тип: {$existingCall->type}, дата: {$existingCall->date}, время: {$existingCall->time})");
            }

            $call = Call::create([
                'type' => CallEnum::TECHNICAL->value,
                'meeting_link' => $params['meeting_link'],
                'date' => $params['date'],
                'time' => $params['time'],
                'candidate_id' => $candidateId,
                'tutor_id' => $taskStatus['tutor_id'],
                'hr_manager_id' => $taskStatus['hr_manager_id']
            ]);

            $tutor = User::with('telegramUser')->findOrFail($taskStatus['tutor_id']);
            $hrManager = User::with('telegramUser')->findOrFail($taskStatus['hr_manager_id']);
            $candidate = User::with('telegramUser')->findOrFail($candidateId);

            $emailData = [
                'candidateName' => $candidate->name,
                'tutorName' => $tutor->name,
                'hrManagerName' => $hrManager->name,
                'date' => $call->date,
                'time' => $call->time,
                'meetingLink' => $call->meeting_link,
                'companyName' => 'ATWINTA'
            ];

            // Отправка уведомлений
            $this->sendEmailNotification($candidate->email, new CallMail($emailData));
            $this->sendTelegramCallNotification($candidate, $call, 'technical');

            $this->sendEmailNotification($tutor->email, new CallMail($emailData));
            $this->sendTelegramCallNotification($tutor, $call, 'technical');

            $this->sendEmailNotification($hrManager->email, new CallMail($emailData));
            $this->sendTelegramCallNotification($hrManager, $call, 'technical');

            return 'Технический созвон назначен.';

        } catch (\Exception $e) {
            Log::error('Ошибка при назначении технического созвона', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Отправка email с обработкой ошибок
     */
    protected function sendEmailNotification($email, $mailable)
    {
        if (empty($email)) {
            Log::warning('Попытка отправки email без указания адреса');
            return;
        }

        try {
            Mail::to($email)->send($mailable);
            Log::info("Email отправлен на {$email}");
        } catch (\Exception $e) {
            Log::error("Ошибка отправки email на {$email}: " . $e->getMessage());
        }
    }

    /**
     * Отправка уведомления в Telegram о изменении статуса задания
     */
    protected function sendTelegramTaskStatusNotification($user, $task, $status, $comment = null, $filePath = null)
    {
        if (!$user->telegram_user_id || !$user->telegramUser) {
            return;
        }

        try {
            $telegram = new Api(config('telegram.bot_token'));
            $siteUrl = env('WEBHOOK_URL', 'https://your-default-site.com');

            $statusMessages = [
                TaskStatusEnum::REVISION->value => '🔄 Задание отправлено на доработку',
                TaskStatusEnum::APPROVED->value => '✅ Задание одобрено',
                TaskStatusEnum::FAILED->value => '❌ Задание не принято',
            ];

            $text = "📢 <b>Статус задания изменен</b>\n\n";
            $text .= "📌 <b>Задание:</b> {$task->task->title}\n";
            $text .= "📝 <b>Статус:</b> {$statusMessages[$status]}\n";

            if ($comment) {
                $text .= "💬 <b>Комментарий:</b>\n{$comment}\n";
            }

            $text .= "\n🔗 <a href='{$siteUrl}'>Перейти на сайт</a>";

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

            Log::info("Telegram уведомление отправлено пользователю {$user->id}");

        } catch (\Exception $e) {
            Log::error("Ошибка отправки Telegram уведомления пользователю {$user->id}: " . $e->getMessage());
        }
    }

    /**
     * Отправка уведомления в Telegram о созвоне
     */
    protected function sendTelegramCallNotification($user, $call, $callType)
    {
        if (!$user->telegram_user_id || !$user->telegramUser) {
            return;
        }

        try {
            $telegram = new Api(config('telegram.bot_token'));

            $callTypes = [
                'final' => '🎯 Финальный созвон',
                'technical' => '🛠 Технический созвон'
            ];

            $text = "📅 <b>{$callTypes[$callType]} назначен</b>\n\n";
            $text .= "📅 <b>Дата:</b> {$call->date}\n";
            $text .= "⏰ <b>Время:</b> {$call->time}\n";
            $text .= "🔗 <b>Ссылка:</b> {$call->meeting_link}\n\n";
            $text .= "Не забудьте присоединиться вовремя!";

            $telegram->sendMessage([
                'chat_id' => $user->telegramUser->telegram_id,
                'text' => $text,
                'parse_mode' => 'HTML'
            ]);

            Log::info("Telegram уведомление о созвоне отправлено пользователю {$user->id}");

        } catch (\Exception $e) {
            Log::error("Ошибка отправки Telegram уведомления о созвоне пользователю {$user->id}: " . $e->getMessage());
        }
    }
}
