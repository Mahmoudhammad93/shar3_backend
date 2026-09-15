<?php

namespace App\Support;

class WebsiteColorPalettes
{
    /** @var array<string, array{id: string, label: string, label_en: string, primary: string, accent: string, background: string}> */
    public const WEBSITE = [
        'institute_navy_gold' => [
            'id' => 'institute_navy_gold',
            'label' => 'أزرق المعهد وذهبي',
            'label_en' => 'Institute Navy & Gold',
            'primary' => '#002B5B',
            'accent' => '#C5A04D',
            'background' => '#f7f9fc',
        ],
        'midnight_scholar' => [
            'id' => 'midnight_scholar',
            'label' => 'أزرق ليلي',
            'label_en' => 'Midnight Scholar',
            'primary' => '#08254b',
            'accent' => '#d4ba84',
            'background' => '#eef1f5',
        ],
        'emerald_classic' => [
            'id' => 'emerald_classic',
            'label' => 'زمردي كلاسيكي',
            'label_en' => 'Emerald Classic',
            'primary' => '#004d40',
            'accent' => '#c9a227',
            'background' => '#f4f7f6',
        ],
        'ocean_calm' => [
            'id' => 'ocean_calm',
            'label' => 'محيط هادئ',
            'label_en' => 'Ocean Calm',
            'primary' => '#1e3a5f',
            'accent' => '#38bdf8',
            'background' => '#f0f9ff',
        ],
        'desert_warm' => [
            'id' => 'desert_warm',
            'label' => 'صحراء دافئة',
            'label_en' => 'Desert Warm',
            'primary' => '#92400e',
            'accent' => '#f59e0b',
            'background' => '#fffbeb',
        ],
    ];

    /** @return list<array{id: string, label: string, label_en: string, primary: string, sidebar: string, accent: string, background: string}> */
    public static function list(): array
    {
        return array_values(array_map(function (array $palette): array {
            return [
                'id' => $palette['id'],
                'label' => $palette['label'],
                'label_en' => $palette['label_en'],
                'primary' => $palette['primary'],
                'sidebar' => $palette['primary'],
                'accent' => $palette['accent'],
                'background' => $palette['background'],
            ];
        }, self::WEBSITE));
    }

    /** @return array{id: string, label: string, label_en: string, primary: string, accent: string, background: string}|null */
    public static function get(?string $paletteId): ?array
    {
        if (! $paletteId || $paletteId === 'custom') {
            return null;
        }

        return self::WEBSITE[$paletteId] ?? null;
    }

    /**
     * @param  array<string, mixed>  $colors
     * @return array<string, mixed>
     */
    public static function apply(array $colors, ?string $paletteId): array
    {
        $palette = self::get($paletteId);

        if (! $palette) {
            return $colors;
        }

        $colors['website_color_palette'] = $paletteId;
        $colors['website_primary_color'] = HexColor::normalize($palette['primary'], $palette['primary']);
        $colors['website_accent_color'] = HexColor::normalize($palette['accent'], $palette['accent']);
        $colors['website_background_color'] = HexColor::normalize($palette['background'], $palette['background']);

        return $colors;
    }

    public static function matchFromColors(?string $primary, ?string $accent, ?string $background): ?string
    {
        if (! $primary || ! $accent || ! $background) {
            return null;
        }

        $primary = HexColor::normalize($primary, $primary);
        $accent = HexColor::normalize($accent, $accent);
        $background = HexColor::normalize($background, $background);

        foreach (self::WEBSITE as $paletteId => $palette) {
            if (
                $primary === HexColor::normalize($palette['primary'], $palette['primary'])
                && $accent === HexColor::normalize($palette['accent'], $palette['accent'])
                && $background === HexColor::normalize($palette['background'], $palette['background'])
            ) {
                return $paletteId;
            }
        }

        return null;
    }

    /** @param  array<string, mixed>  $data */
    public static function resolveStoredPalette(array $data): string
    {
        $matched = self::matchFromColors(
            $data['website_primary_color'] ?? null,
            $data['website_accent_color'] ?? null,
            $data['website_background_color'] ?? null,
        );

        if ($matched !== null) {
            return $matched;
        }

        $stored = $data['website_color_palette'] ?? null;

        if (is_string($stored) && $stored !== '' && $stored !== 'custom' && self::get($stored)) {
            return $stored;
        }

        return 'custom';
    }
}
