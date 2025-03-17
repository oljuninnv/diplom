<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case USER = 'User';
    case WORKER = 'Worker';
    case TUTOR_WORKER = 'Tutor-worker';
    case ADMIN = 'Admin';
}