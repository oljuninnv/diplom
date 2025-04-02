<?php

namespace App\Actions;

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

    public function assignCall(int $id): string
    {
        $application = Application::findOrFail($id);
        $application->update([
            'status' => ApplicationStatusEnum::REJECTED->value
        ]);
        
        return 'Заявка отклонена.';
    }
}