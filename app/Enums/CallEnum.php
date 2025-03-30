<?php

namespace App\Enums;

enum CallEnum: string
{
    case PRIMARY = 'primary';
    case TECHNICAL = 'technical';
    case FINAL = 'final';

    public static function getAll(): array
    {
        return [
            self::PRIMARY->value => self::PRIMARY->value,
            self::TECHNICAL->value => self::TECHNICAL->value,
            self::FINAL->value => self::FINAL->value,
        ];
    }
}