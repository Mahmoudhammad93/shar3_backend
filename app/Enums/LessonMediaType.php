<?php

namespace App\Enums;

enum LessonMediaType: string
{
    case Video = 'video';
    case Audio = 'audio';
    case Text = 'text';

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
