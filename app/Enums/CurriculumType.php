<?php

namespace App\Enums;

enum CurriculumType: string
{
    case General = 'general';
    case Specialized = 'specialized';

    public function labelAr(): string
    {
        return match ($this) {
            self::General => 'عام',
            self::Specialized => 'متخصص',
        };
    }
}
