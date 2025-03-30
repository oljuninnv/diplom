<?php

namespace App\Actions;

use App\Models\Application;
use App\Enums\ApplicationStatusEnum;

class ApplicationAction
{
    /**
     * Одобрить заявку
     */
    public function approve(int $id): string
    {
        $application = Application::findOrFail($id);
        $application->update([
            'status' => ApplicationStatusEnum::APPROVED->value
        ]);
        
        return 'Заявка успешно одобрена.';
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
        
        return 'Заявка отклонена.';
    }
}