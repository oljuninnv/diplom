<?php

namespace App\Actions;

use App\Enums\CallEnum;
use App\Enums\TaskStatusEnum;
use App\Models\Application;
use App\Models\TaskStatus;
use App\Models\Task;
use App\Models\User;
use App\Enums\ApplicationStatusEnum;
use App\Models\Call;
use App\Mail\ApplicationApprovedMail;
use App\Mail\ApplicationRejectedMail;
use App\Mail\ApplicationUnderConsideration;
use App\Mail\UserAddedMail;
use App\Mail\CallMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Telegram\Bot\Api;
use Illuminate\Support\Facades\Log;

class ApplicationAction
{
    protected $telegram;

    public function __construct()
    {
        $this->telegram = new Api(config('telegram.bot_token'));
    }

    /**
     * Одобрить заявку
     */
    public function approve(array $update): string
    {
        try {
            $application = Application::findOrFail($update['id']);
            $task = Task::findOrFail($update['task_id']);
            $user = User::with('telegramUser')->findOrFail($application->user_id);
            if (!$user instanceof User) {
                throw new \RuntimeException('User not found');
            }

            $tutor = User::with('telegramUser')->findOrFail($update['tutor']);
            if (!$tutor instanceof User) {
                throw new \RuntimeException('Tutor not found');
            }

            $hrManager = User::with('telegramUser')->findOrFail($update['hr-manager']);
            if (!$hrManager instanceof User) {
                throw new \RuntimeException('HR Manager not found');
            }

            $endDate = null;
            if ($task->deadline) {
                $endDate = now()->addWeeks($task->deadline)->format('d.m.Y');
            }

            $taskStatus = TaskStatus::create([
                'user_id' => $user->id,
                'tutor_id' => $tutor->id,
                'hr_manager_id' => $hrManager->id,
                'task_id' => $task->id,
                'status' => TaskStatusEnum::IN_PROGRESS->value,
                'end_date' => $endDate ? now()->addWeeks($task->deadline)->format('Y-m-d') : null,
            ]);

            $user->update(['password' => Hash::make('password')]);
            $application->update(['status' => ApplicationStatusEnum::APPROVED->value]);

            // Отправка email уведомлений
            Mail::to($user->email)->send(
                new ApplicationApprovedMail($user, $tutor, $hrManager, $task)
            );

            Mail::send(
                new UserAddedMail($user, $tutor, $hrManager, $task, $endDate)
            );

            // Отправка Telegram уведомлений
            $this->sendApprovalTelegramNotifications($user, $tutor, $hrManager, $task, $taskStatus);

            return 'Заявка успешно одобрена. Сообщения отправлены.';

        } catch (\Exception $e) {
            Log::error("Error approving application: " . $e->getMessage());
            return 'Произошла ошибка при обработке заявки.';
        }
    }

    /**
     * Отправка Telegram уведомлений при одобрении заявки
     */
    protected function sendApprovalTelegramNotifications($user, $tutor, $hrManager, $task, $taskStatus)
    {
        try {
            $siteUrl = env('WEBHOOK_URL', 'https://your-default-site.com');

            // Уведомление кандидату
            if ($user->telegramUser) {
                $text = "🎉 Ваша заявка одобрена!\n\n";
                $text .= "📌 Задание: {$task->title}\n";
                $text .= "👨‍🏫 Тьютор: {$tutor->name}\n";
                $text .= "📅 Срок выполнения: {$taskStatus->end_date}\n\n";
                $text .= "🔑 <b>Ваши учетные данные:</b>\n";
                $text .= "📧 Логин: {$user->email}\n";
                $text .= "🔒 Пароль: password\n\n";
                $text .= "⚠️ <b>Важно:</b>\n";
                $text .= "Пожалуйста, беспокойте тьютора и HR-менеджера только в крайнем случае, если у вас возникли действительно серьезные трудности при выполнении задания.\n\n";
                $text .= "Частота и характер ваших обращений будут учитываться при оценке выполнения тестового задания.\n\n";
                $text .= "🔗 <a href='{$siteUrl}'>Перейти на сайт</a>";

                $this->telegram->sendMessage([
                    'chat_id' => $user->telegramUser->telegram_id,
                    'text' => $text,
                    'parse_mode' => 'HTML'
                ]);
            }

            // Уведомление тьютору
            if ($tutor->telegramUser) {
                $text = "📢 Вам назначен новый студент!\n\n";
                $text .= "👤 Студент: {$user->name}\n";
                $text .= "📌 Задание: {$task->title}\n";
                $text .= "📅 Срок выполнения: {$taskStatus->end_date}\n\n";
                $text .= "🔗 <a href='{$siteUrl}'>Перейти к списку студентов</a>";

                $this->telegram->sendMessage([
                    'chat_id' => $tutor->telegramUser->telegram_id,
                    'text' => $text,
                    'parse_mode' => 'HTML'
                ]);
            }

            // Уведомление HR-менеджеру
            if ($hrManager->telegramUser) {
                $text = "📝 Новая заявка одобрена\n\n";
                $text .= "👤 Кандидат: {$user->name}\n";
                $text .= "👨‍🏫 Тьютор: {$tutor->name}\n";
                $text .= "📌 Задание: {$task->title}\n";
                $text .= "📅 Срок: {$taskStatus->end_date}\n\n";
                $text .= "🔗 <a href='{$siteUrl}/hr/applications'>Перейти к заявкам</a>";

                $this->telegram->sendMessage([
                    'chat_id' => $hrManager->telegramUser->telegram_id,
                    'text' => $text,
                    'parse_mode' => 'HTML'
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Telegram notification error: " . $e->getMessage());
        }
    }

    /**
     * Отклонить заявку
     */
    public function decline(int $id): string
    {
        try {
            $application = Application::findOrFail($id);
            $application->update([
                'status' => ApplicationStatusEnum::REJECTED->value
            ]);

            $user = User::with('telegramUser')->findOrFail($application->user_id);
            if (!$user instanceof User) {
                throw new \RuntimeException('User not found');
            }


            // Отправка email
            Mail::to($user->email)->send(
                new ApplicationRejectedMail($user)
            );

            // Отправка Telegram уведомления
            if ($user->telegramUser) {
                $text = "😔 К сожалению, ваша заявка была отклонена.\n\n";
                $text .= "Вы можете подать новую заявку или связаться с HR для уточнения деталей.";

                $this->telegram->sendMessage([
                    'chat_id' => $user->telegramUser->telegram_id,
                    'text' => $text,
                    'parse_mode' => 'HTML'
                ]);
            }

            return 'Заявка отклонена. Уведомления отправлены.';

        } catch (\Exception $e) {
            Log::error("Error declining application: " . $e->getMessage());
            return 'Произошла ошибка при отклонении заявки.';
        }
    }

    public function underConsideration(int $id)
    {
        try {
            $application = Application::findOrFail($id);
            $application->update([
                'status' => ApplicationStatusEnum::UnderConsideration->value
            ]);

            $user = User::with('telegramUser')->findOrFail($application->user_id);
            if (!$user instanceof User) {
                throw new \RuntimeException('User not found');
            }


            // Отправка email
            Mail::to($user->email)->send(
                new ApplicationUnderConsideration($user)
            );

            // Отправка Telegram уведомления
            if ($user->telegramUser) {
                $text = "😊 Поздравляем, ваша заявка была взята на рассмотрение.\n\n";
                $text .= "Ожидайте, когда с вами свяжется наш hr-менеджер для назначения первичного собеседования.";

                $this->telegram->sendMessage([
                    'chat_id' => $user->telegramUser->telegram_id,
                    'text' => $text,
                    'parse_mode' => 'HTML'
                ]);
            }

            return 'Заявка принята на рассмотрение. Уведомления отправлены.';

        } catch (\Exception $e) {
            Log::error("Error declining application: " . $e->getMessage());
            return 'Произошла ошибка при взятии на рассмотрении заявки заявки.';
        }
    }

    public function assignCall(int $id, array $array): string
    {
        Log::info('Начало назначения созвона', ['application_id' => $id, 'input_data' => $array]);

        try {
            $application = Application::findOrFail($id);
            Log::info('Заявка найдена', ['application' => $application->toArray()]);

            $callData = [
                'type' => CallEnum::PRIMARY->value,
                'meeting_link' => $array['meeting_link'],
                'date' => $array['date'],
                'time' => $array['time'],
                'candidate_id' => $application['user_id'],
                'hr_manager_id' => $array['hr-manager'],
                'tutor_id' => $array['tutor'] ?? null // Делаем tutor_id необязательным
            ];

            $call = Call::create($callData);
            Log::info('Созвон создан', ['call' => $call->toArray()]);

            $hrManager = User::with('telegramUser')->findOrFail($array['hr-manager']);
            $candidate = $application->user()->with('telegramUser')->first();

            $users = [
                'hr_manager' => $hrManager,
                'candidate' => $candidate
            ];

            // Добавляем тьютора только если он указан
            if (!empty($array['tutor'])) {
                $tutor = User::with('telegramUser')->findOrFail($array['tutor']);
                $users['tutor'] = $tutor;
            }

            Log::info('Пользователи найдены', array_map(fn($user) => $user->toArray(), $users));

            $emailData = [
                'candidateName' => $candidate->name,
                'tutorName' => $users['tutor']->name ?? null, // Учитываем отсутствие тьютора
                'hrManagerName' => $hrManager->name,
                'date' => $call->date,
                'time' => $call->time,
                'meetingLink' => $call->meeting_link,
                'companyName' => 'ATWINTA'
            ];

            // Отправка уведомлений
            $this->sendCallNotifications(
                $candidate,
                $users['tutor'] ?? null, // Передаем null если тьютора нет
                $hrManager,
                $call,
                'primary'
            );

            return 'Созвон назначен. Уведомления отправлены.';

        } catch (\Exception $e) {
            Log::error('Ошибка при назначении созвона', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Отправка уведомлений о созвоне
     */
    protected function sendCallNotifications($candidate, $tutor, $hrManager, $call, $callType)
    {
        $callTypes = [
            'primary' => 'Первичный созвон',
            'technical' => 'Технический созвон',
            'final' => 'Финальный созвон'
        ];

        $text = "📅 <b>{$callTypes[$callType]}</b>\n\n";
        $text .= "🕒 <b>Дата и время:</b> {$call->date} в {$call->time}\n";
        $text .= "🔗 <b>Ссылка:</b> {$call->meeting_link}\n\n";
        $text .= "Пожалуйста, не опаздывайте!";

        // Кандидату
        if ($candidate->telegramUser) {
            $this->telegram->sendMessage([
                'chat_id' => $candidate->telegramUser->telegram_id,
                'text' => $text,
                'parse_mode' => 'HTML'
            ]);
        }

        // Тьютору (если есть)
        if ($tutor && $tutor->telegramUser) {
            $this->telegram->sendMessage([
                'chat_id' => $tutor->telegramUser->telegram_id,
                'text' => $text,
                'parse_mode' => 'HTML'
            ]);
        }

        // HR-менеджеру
        if ($hrManager->telegramUser) {
            $this->telegram->sendMessage([
                'chat_id' => $hrManager->telegramUser->telegram_id,
                'text' => $text,
                'parse_mode' => 'HTML'
            ]);
        }

        // Отправка email
        $emailData = [
            'candidateName' => $candidate->name,
            'tutorName' => $tutor->name ?? null, // Учитываем отсутствие тьютора
            'hrManagerName' => $hrManager->name,
            'date' => $call->date,
            'time' => $call->time,
            'meetingLink' => $call->meeting_link,
            'companyName' => 'ATWINTA'
        ];

        if ($candidate->email) {
            Mail::to($candidate->email)->send(new CallMail($emailData));
        }
        if ($tutor && $tutor->email) {
            Mail::to($tutor->email)->send(new CallMail($emailData));
        }
        if ($hrManager->email) {
            Mail::to($hrManager->email)->send(new CallMail($emailData));
            // Mail::send(new CallMail($emailData));
        }
    }
}