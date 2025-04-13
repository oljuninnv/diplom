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
use App\Mail\UserAddedMail;
use App\Mail\CallMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class ApplicationAction
{
    /**
     * Одобрить заявку
     */
    public function approve(array $update): string
    {
        try {
            \Log::info($update);

            $application = Application::findOrFail($update['id']);
            $task = Task::findOrFail($update['task_id']);
            $user = User::findOrFail($application->user_id);
            $tutor = User::findOrFail($update['tutor']);
            $hrManager = User::findOrFail($update['hr-manager']);

            $endDate = null;
            if ($task->deadline) {
                $endDate = now()->addWeeks($task->deadline)->format('d.m.Y');
            }

            TaskStatus::create([
                'user_id' => $user->id,
                'tutor_id' => $tutor->id,
                'hr_manager_id' => $hrManager->id,
                'task_id' => $task->id,
                'status' => TaskStatusEnum::IN_PROGRESS->value,
                'end_date' => $endDate ? now()->addWeeks($task->deadline)->format('Y-m-d') : null,
            ]);

            $user->update(['password' => Hash::make('password')]);
            $application->update(['status' => ApplicationStatusEnum::APPROVED->value]);

            // Отправка письма пользователю
            Mail::to($user->email)->send(
                new ApplicationApprovedMail($user, $tutor, $hrManager, $task)
            );

            //Отправка письма тьютору
            // Mail::to($tutor->email)->send(
            //     new ApplicationApprovedMail($user, $tutor, $hrManager, $task)
            // );

            // Отправка письма администратору
            Mail::send(
                new UserAddedMail($user, $tutor, $hrManager, $task, $endDate)
            );

            return 'Заявка успешно одобрена. Сообщения отправлены.';

        } catch (\Exception $e) {
            \Log::error("Error approving application: " . $e->getMessage());
            return 'Произошла ошибка при обработке заявки.';
        }
    }

    /**
     * Отклонить заявку
     */
    public function decline(int $id): string
    {
        $application = Application::findOrFail($id);
        $application->update([
            'status' => ApplicationStatusEnum::REJECTED->value
        ]);

        $user = User::findOrFail($application->user_id);

        Mail::to($user->email)->send(
            new ApplicationRejectedMail($user)
        );

        return 'Заявка отклонена.';
    }

    public function assignCall(int $id, array $array): string
    {
        \Log::info('Начало назначения созвона', ['application_id' => $id, 'input_data' => $array]);

        try {
            $application = Application::findOrFail($id);
            \Log::info('Заявка найдена', ['application' => $application->toArray()]);

            $call = Call::create([
                'type' => CallEnum::PRIMARY->value,
                'meeting_link' => $array['meeting_link'],
                'date' => $array['date'],
                'time' => $array['time'],
                'candidate_id' => $application['user_id'],
                'tutor_id' => $array['tutor'],
                'hr_manager_id' => $array['hr-manager']
            ]);
            \Log::info('Созвон создан', ['call' => $call->toArray()]);

            $tutor = User::findOrFail($array['tutor']);
            $hrManager = User::findOrFail($array['hr-manager']);
            $candidate = $application->user;
            \Log::info('Пользователи найдены', [
                'tutor' => $tutor->toArray(),
                'hr_manager' => $hrManager->toArray(),
                'candidate' => $candidate->toArray()
            ]);

            $emailData = [
                'candidateName' => $candidate->name,
                'tutorName' => $tutor->name,
                'hrManagerName' => $hrManager->name,
                'date' => $call->date,
                'time' => $call->time,
                'meetingLink' => $call->meeting_link,
                'companyName' => 'ATWINTA'
            ];

            // Отправка письма кандидату
            if ($candidate->email) {
                Mail::to($candidate->email)->send(new CallMail($emailData));
                \Log::info('Письмо отправлено кандидату', ['email' => $candidate->email]);
            }

            // Отправка письма тьютору
            if ($tutor->email) {
                Mail::to($tutor->email)->send(new CallMail($emailData));
                \Log::info('Письмо отправлено тьютору', ['email' => $tutor->email]);
            }

            // Отправка письма HR-менеджеру
            if ($hrManager->email) {
                Mail::to($hrManager->email)->send(new CallMail($emailData));
                \Log::info('Письмо отправлено HR-менеджеру', ['email' => $hrManager->email]);
            }

            return 'Созвон назначен.';

        } catch (\Exception $e) {
            \Log::error('Ошибка при назначении созвона', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}