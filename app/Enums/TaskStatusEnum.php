<?php

namespace App\Enums;

enum TaskStatusEnum: string
{
    case IN_PROGRESS = 'в процессе';
    case UNDER_REVIEW = 'на проверке';
    case APPROVED = 'одобрено';
    case REVISION = 'доработка';
    case FAILED = 'провалено';
    case ADOPTED = 'принят';

    public static function getAll(): array
    {
        return [
            self::IN_PROGRESS->value => self::IN_PROGRESS->value,
            self::UNDER_REVIEW->value => self::UNDER_REVIEW->value,
            self::APPROVED->value => self::APPROVED->value,
            self::REVISION->value => self::REVISION->value,
            self::FAILED->value => self::FAILED->value,
            self::ADOPTED->value => self::ADOPTED->value,
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