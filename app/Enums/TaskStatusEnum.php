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

    public static function getAll(): array
    {
        return [
            self::IN_PROGRESS->value => self::IN_PROGRESS->name,
            self::UNDER_REVIEW->value => self::UNDER_REVIEW->name,
            self::APPROVED->value => self::APPROVED->name,
            self::REVISION->value => self::REVISION->name,
            self::COMPLETED->value => self::COMPLETED->name,
            self::FAILED->value => self::FAILED->name,
        ];
    }
}