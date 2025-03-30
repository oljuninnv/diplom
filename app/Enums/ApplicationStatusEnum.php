<?php

namespace App\Enums;

enum ApplicationStatusEnum: string
{
    case PENDING = 'ожидание';
    case APPROVED = 'одобрено';
    case REJECTED = 'отклонено';

    public static function getAll(): array
    {
        return [
            self::PENDING->value => self::PENDING->value,
            self::APPROVED->value => self::APPROVED->value,
            self::REJECTED->value => self::REJECTED->value,
        ];
    }
}