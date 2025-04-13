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

class TaskStatusAction
{
    public function revision(array $params)
    {
        $task = TaskStatus::findOrFail($params['id']);
        $task->update(['status' => TaskStatusEnum::REVISION->value]);

        $user = User::findOrFail($task->user_id);

        Mail::to($user->email)->send(
            new TaskStatusMail(
                $user,
                'revision',
                $task->id,
                $params['comment'] ?? null,
                $params['file_path'] ?? null
            )
        );

        return 'Задание отправлено на доработку';
    }

    public function approved(array $params)
    {
        $task = TaskStatus::findOrFail($params['id']);
        $task->update(['status' => TaskStatusEnum::APPROVED->value]);

        $user = User::findOrFail($task->user_id);

        Report::create([
            'tutor_id' => auth()->id(),
            'user_id' => $user->id,
            'task_id' => $task->id,
            'report' => $params['file_path']
        ]);

        Mail::to($user->email)->send(
            new TaskStatusMail(
                $user,
                'approved',
                $task->id,
                null,
                $params['file_path'] ?? null
            )
        );

        return 'Задание одобрено';
    }

    public function failed(array $params)
    {
        $task = TaskStatus::findOrFail($params['id']);
        $task->update(['status' => TaskStatusEnum::FAILED->value]);

        $user = User::findOrFail($task->user_id);

        Report::create([
            'tutor_id' => auth()->id(),
            'user_id' => $user->id,
            'task_id' => $task->id,
            'report' => $params['file_path']
        ]);

        Mail::to($user->email)->send(
            new TaskStatusMail(
                $user,
                'failed',
                $task->id,
                $params['comment'] ?? null,
                $params['file_path'] ?? null
            )
        );

        return 'Задание провалено';
    }

    public function final_call(array $params)
    {
        try {
            $taskStatus = TaskStatus::findOrFail($params['id']);

            $call = Call::create([
                'type' => CallEnum::FINAL ->value,
                'meeting_link' => $params['meeting_link'],
                'date' => $params['date'],
                'time' => $params['time'],
                'candidate_id' => $taskStatus['user_id'],
                'tutor_id' => $taskStatus['tutor_id'],
                'hr_manager_id' => $taskStatus['hr_manager_id']
            ]);
            \Log::info('Созвон создан', ['call' => $call->toArray()]);

            $tutor = User::findOrFail($taskStatus['tutor_id']);
            $hrManager = User::findOrFail($taskStatus['hr_manager_id']);
            $candidate = User::findOrFail($taskStatus['user_id']);
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

            // // Отправка письма HR-менеджеру
            if ($hrManager->email) {
                Mail::to($hrManager->email)->send(new CallMail($emailData));
                \Log::info('Письмо отправлено HR-менеджеру', ['email' => $hrManager->email]);
            }

            return 'Финальный созвон назначен.';

        } catch (\Exception $e) {
            \Log::error('Ошибка при назначении созвона', [
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

            $call = Call::create([
                'type' => CallEnum::TECHNICAL->value,
                'meeting_link' => $params['meeting_link'],
                'date' => $params['date'],
                'time' => $params['time'],
                'candidate_id' => $taskStatus['user_id'],
                'tutor_id' => $taskStatus['tutor_id'],
                'hr_manager_id' => $taskStatus['hr_manager_id']
            ]);
            \Log::info('Созвон создан', ['call' => $call->toArray()]);

            $tutor = User::findOrFail($taskStatus['tutor_id']);
            $hrManager = User::findOrFail($taskStatus['hr_manager_id']);
            $candidate = User::findOrFail($taskStatus['user_id']);
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

            // // Отправка письма HR-менеджеру
            if ($hrManager->email) {
                Mail::to($hrManager->email)->send(new CallMail($emailData));
                \Log::info('Письмо отправлено HR-менеджеру', ['email' => $hrManager->email]);
            }

            return 'Техничский созвон назначен.';

        } catch (\Exception $e) {
            \Log::error('Ошибка при назначении созвона', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}