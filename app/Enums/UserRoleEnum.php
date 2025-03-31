<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case USER = 'User';
    case WORKER = 'Worker';
    case TUTOR_WORKER = 'Tutor-worker';
    case ADMIN = 'Admin';
    case SUPER_ADMIN = 'Super-Admin';

    public static function getAll(): array
    {
        return [
            self::USER->value => self::USER->value,
            self::WORKER->value => self::WORKER->value,
            self::TUTOR_WORKER->value => self::TUTOR_WORKER->value,
            self::ADMIN->value => self::ADMIN->value,
            self::SUPER_ADMIN->value => self::SUPER_ADMIN->value,
        ];
    }
}