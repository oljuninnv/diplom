<?php 

namespace App\Enums;

enum LevelOfExperienceEnum: string
{
    case junior = 'junior';
    case middle = 'middle';
    case senior = 'senior';

    public static function getAll(): array
    {
        return [
            self::junior->value => self::junior->name,
            self::middle->value => self::middle->name,
            self::senior->value => self::senior->name,
        ];
    }
}