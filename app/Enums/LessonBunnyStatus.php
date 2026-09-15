<?php

namespace App\Enums;

enum LessonBunnyStatus: string
{
    case Created = 'created';
    case Processing = 'processing';
    case Ready = 'ready';
    case Failed = 'failed';

    public function labelAr(): string
    {
        return match ($this) {
            self::Created => 'لم يُرفع بعد',
            self::Processing => 'قيد المعالجة',
            self::Ready => 'جاهز',
            self::Failed => 'فشل',
        };
    }

    public static function fromBunnyStreamStatus(int $status): self
    {
        return match ($status) {
            3, 4 => self::Ready,
            5, 8 => self::Failed,
            0, 1, 2, 6, 7 => self::Processing,
            default => self::Processing,
        };
    }
}
