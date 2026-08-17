<?php

namespace App\Enums;

enum StudentSpecializationStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    public function labelAr(): string
    {
        return match ($this) {
            self::Active => 'نشط',
            self::Inactive => 'غير نشط',
        };
    }
}
