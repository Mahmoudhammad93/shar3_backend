<?php

namespace App\Support;

class HexColor
{
    public static function normalize(?string $color, string $fallback = '#000000'): string
    {
        if ($color === null || trim($color) === '') {
            return $fallback;
        }

        $hex = trim($color);

        if (preg_match('/^#?([0-9a-fA-F]{6})#?$/', $hex, $matches)) {
            return '#'.strtolower($matches[1]);
        }

        if (preg_match('/^#?([0-9a-fA-F]{3})#?$/', $hex, $matches)) {
            $short = strtolower($matches[1]);

            return sprintf(
                '#%s%s%s',
                $short[0].$short[0],
                $short[1].$short[1],
                $short[2].$short[2],
            );
        }

        return $fallback;
    }
}
