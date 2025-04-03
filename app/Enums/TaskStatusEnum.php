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
            self::IN_PROGRESS->value => self::IN_PROGRESS->value,
            self::UNDER_REVIEW->value => self::UNDER_REVIEW->value,
            self::APPROVED->value => self::APPROVED->value,
            self::REVISION->value => self::REVISION->value,
            self::COMPLETED->value => self::COMPLETED->value,
            self::FAILED->value => self::FAILED->value,
        ];
    }

    public static function changeStatus(): array
    {
        return [
            self::APPROVED->value => self::APPROVED->value,
            self::REVISION->value => self::REVISION->value,
            self::FAILED->value => self::FAILED->value,
        ];
    }
}