<?php

namespace App\Enums;

enum TaskStatusEnum: string
{
    case IN_PROGRESS = 'в процессе';
    case UNDER_REVIEW = 'на проверке';
    case APPROVED = 'одобрено';
    case REVISION = 'доработка';
    case COMPLETED = 'выполнено';
    case FAILED = 'провалено';
}