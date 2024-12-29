<?php

namespace App\enums;


enum GenderEnum: string
{
    case MALE = 'male';
    case FEMALE = 'female';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::MALE->value => 'Male',
            self::FEMALE->value => 'Female',
        ];
    }
}
