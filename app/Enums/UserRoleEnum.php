<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case USER = 'User';
    case WORKER = 'Worker';
    case TUTOR_WORKER = 'Tutor-worker';
    case ADMIN = 'Admin';

    public static function getAll(): array
    {
        return [
            self::USER->value => self::USER->name,
            self::WORKER->value => self::WORKER->name,
            self::TUTOR_WORKER->value => self::TUTOR_WORKER->name,
            self::ADMIN->value => self::ADMIN->name,
        ];
    }
}