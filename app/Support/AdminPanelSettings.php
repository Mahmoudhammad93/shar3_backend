<?php

namespace App\Support;

use App\Models\SiteSetting;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;

class AdminPanelSettings
{
    public static function settings(): SiteSetting
    {
        return SiteSetting::current();
    }

    public static function forgetCache(): void
    {
        // Reserved for future cache layer; settings are read live from DB.
    }

    public static function brandName(): string
    {
        $settings = static::settings();

        return $settings->admin_brand_name_ar
            ?: $settings->site_name_ar
            ?: 'معهد علم شرعي';
    }

    public static function brandLogoUrl(): ?string
    {
        $settings = static::settings();

        if ($settings->admin_use_site_logo !== false && $settings->logo) {
            return static::mediaUrl($settings->logo);
        }

        if ($settings->admin_logo) {
            return static::mediaUrl($settings->admin_logo);
        }

        return $settings->logo ? static::mediaUrl($settings->logo) : null;
    }

    public static function brandLogoHeight(): ?string
    {
        return static::brandLogoUrl() ? '2.5rem' : null;
    }

    public static function primaryColor(): array
    {
        $hex = static::settings()->admin_primary_color ?: '#059669';

        return Color::hex($hex);
    }

    public static function maxContentWidth(): Width|string
    {
        return match (static::settings()->admin_layout) {
            'container' => Width::SevenExtraLarge,
            'full' => Width::Full,
            default => Width::Full,
        };
    }

    public static function usesTopNavigation(): bool
    {
        return static::settings()->admin_navigation === 'top';
    }

    public static function sidebarCollapsible(): bool
    {
        return static::settings()->admin_sidebar_collapsible !== false;
    }

    public static function sidebarWidth(): string
    {
        return static::settings()->admin_compact_mode ? '16rem' : '18rem';
    }

    public static function themeVariables(): array
    {
        $settings = static::settings();
        $sidebar = $settings->admin_sidebar_color ?: '#0f172a';

        return [
            'primary' => $settings->admin_primary_color ?: '#059669',
            'sidebar' => $sidebar,
            'sidebarDark' => static::isLightColor($sidebar) ? '#0f172a' : $sidebar,
            'accent' => $settings->admin_accent_color ?: '#c9a227',
            'background' => $settings->admin_background_color ?: '#f8fafc',
            'style' => $settings->admin_style ?: 'classic',
            'sidebarStyle' => $settings->admin_sidebar_style ?: 'dark',
            'showPattern' => $settings->admin_show_pattern === true,
            'compactMode' => $settings->admin_compact_mode === true,
        ];
    }

    protected static function isLightColor(string $hex): bool
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        if (strlen($hex) !== 6) {
            return false;
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        return $luminance > 0.6;
    }

    protected static function mediaUrl(?string $path): ?string
    {
        return MediaUrl::resolve($path);
    }
}
