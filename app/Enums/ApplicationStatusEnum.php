<?php

namespace App\Enums;

enum ApplicationStatusEnum: string
{
    case PENDING = 'ожидание';
    case APPROVED = 'одобрено';
    case REJECTED = 'отклонено';
}