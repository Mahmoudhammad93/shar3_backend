<?php

namespace App\Support;

class DashboardColorPalettes
{
    /** @var array<string, array{id: string, label: string, label_en: string, primary: string, sidebar: string, accent: string, background: string, sidebar_style?: string}> */
    public const STUDENT = [
        'emerald_classic' => [
            'id' => 'emerald_classic',
            'label' => 'زمردي كلاسيكي',
            'label_en' => 'Emerald Classic',
            'primary' => '#004d40',
            'sidebar' => '#0a3d34',
            'accent' => '#c9a227',
            'background' => '#f4f7f6',
            'sidebar_style' => 'dark',
        ],
        'midnight_scholar' => [
            'id' => 'midnight_scholar',
            'label' => 'أزرق ليلي',
            'label_en' => 'Midnight Scholar',
            'primary' => '#08254b',
            'sidebar' => '#061833',
            'accent' => '#d4ba84',
            'background' => '#eef1f5',
            'sidebar_style' => 'dark',
        ],
        'teal_modern' => [
            'id' => 'teal_modern',
            'label' => 'تركواز عصري',
            'label_en' => 'Teal Modern',
            'primary' => '#0d9488',
            'sidebar' => '#115e59',
            'accent' => '#fbbf24',
            'background' => '#f0fdfa',
            'sidebar_style' => 'dark',
        ],
        'forest_heritage' => [
            'id' => 'forest_heritage',
            'label' => 'غابة تراثية',
            'label_en' => 'Forest Heritage',
            'primary' => '#1b4332',
            'sidebar' => '#2d6a4f',
            'accent' => '#e9c46a',
            'background' => '#f1faee',
            'sidebar_style' => 'dark',
        ],
        'ocean_calm' => [
            'id' => 'ocean_calm',
            'label' => 'محيط هادئ',
            'label_en' => 'Ocean Calm',
            'primary' => '#1e3a5f',
            'sidebar' => '#1e40af',
            'accent' => '#38bdf8',
            'background' => '#f0f9ff',
            'sidebar_style' => 'dark',
        ],
        'desert_warm' => [
            'id' => 'desert_warm',
            'label' => 'صحراء دافئة',
            'label_en' => 'Desert Warm',
            'primary' => '#92400e',
            'sidebar' => '#78350f',
            'accent' => '#f59e0b',
            'background' => '#fffbeb',
            'sidebar_style' => 'dark',
        ],
        'slate_elegant' => [
            'id' => 'slate_elegant',
            'label' => 'أردواز أنيق',
            'label_en' => 'Slate Elegant',
            'primary' => '#334155',
            'sidebar' => '#cbd5e1',
            'accent' => '#64748b',
            'background' => '#f8fafc',
            'sidebar_style' => 'light',
        ],
        'burgundy_academic' => [
            'id' => 'burgundy_academic',
            'label' => 'عنابي أكاديمي',
            'label_en' => 'Burgundy Academic',
            'primary' => '#7f1d1d',
            'sidebar' => '#450a0a',
            'accent' => '#ca8a04',
            'background' => '#fef2f2',
            'sidebar_style' => 'dark',
        ],
        'sage_soft' => [
            'id' => 'sage_soft',
            'label' => 'مريمية ناعمة',
            'label_en' => 'Sage Soft',
            'primary' => '#4a6741',
            'sidebar' => '#d4e2cc',
            'accent' => '#a3b18a',
            'background' => '#f6f7f2',
            'sidebar_style' => 'light',
        ],
        'navy_gold' => [
            'id' => 'navy_gold',
            'label' => 'كحلي وذهبي',
            'label_en' => 'Navy Gold',
            'primary' => '#1e3a8a',
            'sidebar' => '#172554',
            'accent' => '#d4af37',
            'background' => '#eff6ff',
            'sidebar_style' => 'dark',
        ],
    ];

    /** @var array<string, array{id: string, label: string, label_en: string, primary: string, sidebar: string, accent: string, background: string, sidebar_style?: string}> */
    public const ADMIN = [
        'emerald_admin' => [
            'id' => 'emerald_admin',
            'label' => 'زمردي إداري',
            'label_en' => 'Emerald Admin',
            'primary' => '#059669',
            'sidebar' => '#0f172a',
            'accent' => '#c9a227',
            'background' => '#f8fafc',
            'sidebar_style' => 'dark',
        ],
        'indigo_pro' => [
            'id' => 'indigo_pro',
            'label' => 'نيلي احترافي',
            'label_en' => 'Indigo Pro',
            'primary' => '#4f46e5',
            'sidebar' => '#312e81',
            'accent' => '#818cf8',
            'background' => '#f5f3ff',
            'sidebar_style' => 'dark',
        ],
        'slate_command' => [
            'id' => 'slate_command',
            'label' => 'أردواز قيادي',
            'label_en' => 'Slate Command',
            'primary' => '#475569',
            'sidebar' => '#0f172a',
            'accent' => '#94a3b8',
            'background' => '#f1f5f9',
            'sidebar_style' => 'dark',
        ],
        'carbon_dark' => [
            'id' => 'carbon_dark',
            'label' => 'كربون داكن',
            'label_en' => 'Carbon Dark',
            'primary' => '#18181b',
            'sidebar' => '#09090b',
            'accent' => '#a1a1aa',
            'background' => '#fafafa',
            'sidebar_style' => 'dark',
        ],
        'ocean_ops' => [
            'id' => 'ocean_ops',
            'label' => 'عمليات محيطية',
            'label_en' => 'Ocean Ops',
            'primary' => '#0369a1',
            'sidebar' => '#0c4a6e',
            'accent' => '#38bdf8',
            'background' => '#f0f9ff',
            'sidebar_style' => 'dark',
        ],
        'violet_studio' => [
            'id' => 'violet_studio',
            'label' => 'بنفسجي استوديو',
            'label_en' => 'Violet Studio',
            'primary' => '#7c3aed',
            'sidebar' => '#4c1d95',
            'accent' => '#a78bfa',
            'background' => '#faf5ff',
            'sidebar_style' => 'dark',
        ],
        'teal_control' => [
            'id' => 'teal_control',
            'label' => 'تركواز تحكم',
            'label_en' => 'Teal Control',
            'primary' => '#0f766e',
            'sidebar' => '#134e4a',
            'accent' => '#2dd4bf',
            'background' => '#f0fdfa',
            'sidebar_style' => 'dark',
        ],
        'amber_warm' => [
            'id' => 'amber_warm',
            'label' => 'كهرماني دافئ',
            'label_en' => 'Amber Warm',
            'primary' => '#d97706',
            'sidebar' => '#78350f',
            'accent' => '#fcd34d',
            'background' => '#fffbeb',
            'sidebar_style' => 'dark',
        ],
        'rose_executive' => [
            'id' => 'rose_executive',
            'label' => 'وردي تنفيذي',
            'label_en' => 'Rose Executive',
            'primary' => '#be123c',
            'sidebar' => '#881337',
            'accent' => '#fda4af',
            'background' => '#fff1f2',
            'sidebar_style' => 'dark',
        ],
        'midnight_blue' => [
            'id' => 'midnight_blue',
            'label' => 'أزرق منتصف الليل',
            'label_en' => 'Midnight Blue',
            'primary' => '#1e40af',
            'sidebar' => '#172554',
            'accent' => '#60a5fa',
            'background' => '#eff6ff',
            'sidebar_style' => 'dark',
        ],
    ];

    public static function student(?string $id): ?array
    {
        if ($id === null || $id === '' || $id === 'custom') {
            return null;
        }

        return self::STUDENT[$id] ?? null;
    }

    public static function admin(?string $id): ?array
    {
        if ($id === null || $id === '' || $id === 'custom') {
            return null;
        }

        return self::ADMIN[$id] ?? null;
    }

    /** @return array<string, string> */
    public static function studentSelectOptions(): array
    {
        $options = ['custom' => 'مخصص — ألوان يدوية'];

        foreach (self::STUDENT as $palette) {
            $options[$palette['id']] = $palette['label'];
        }

        return $options;
    }

    /** @return array<string, string> */
    public static function adminSelectOptions(): array
    {
        $options = ['custom' => 'مخصص — ألوان يدوية'];

        foreach (self::ADMIN as $palette) {
            $options[$palette['id']] = $palette['label'];
        }

        return $options;
    }

    /** @return list<array{id: string, label: string, label_en: string, primary: string, sidebar: string, accent: string, background: string, sidebar_style?: string}> */
    public static function studentList(): array
    {
        return array_values(self::STUDENT);
    }

    /** @return list<array{id: string, label: string, label_en: string, primary: string, sidebar: string, accent: string, background: string, sidebar_style?: string}> */
    public static function adminList(): array
    {
        return array_values(self::ADMIN);
    }

    /**
     * @param  array<string, mixed>  $colors
     * @return array<string, mixed>
     */
    public static function applyStudentPalette(array $colors, ?string $paletteId): array
    {
        $palette = self::student($paletteId);

        if (! $palette) {
            return $colors;
        }

        return self::mergePalette($colors, $palette, 'dashboard');
    }

    /**
     * @param  array<string, mixed>  $colors
     * @return array<string, mixed>
     */
    public static function applyAdminPalette(array $colors, ?string $paletteId): array
    {
        $palette = self::admin($paletteId);

        if (! $palette) {
            return $colors;
        }

        return self::mergePalette($colors, $palette, 'admin');
    }

    /**
     * @param  array<string, mixed>  $colors
     * @param  array{id: string, label: string, label_en: string, primary: string, sidebar: string, accent: string, background: string, sidebar_style?: string}  $palette
     * @return array<string, mixed>
     */
    protected static function mergePalette(array $colors, array $palette, string $prefix): array
    {
        $colors["{$prefix}_primary_color"] = HexColor::normalize($palette['primary'], $palette['primary']);
        $colors["{$prefix}_sidebar_color"] = HexColor::normalize($palette['sidebar'], $palette['sidebar']);
        $colors["{$prefix}_accent_color"] = HexColor::normalize($palette['accent'], $palette['accent']);
        $colors["{$prefix}_background_color"] = HexColor::normalize($palette['background'], $palette['background']);

        if (isset($palette['sidebar_style'])) {
            $colors["{$prefix}_sidebar_style"] = $palette['sidebar_style'];
        }

        return $colors;
    }
}
