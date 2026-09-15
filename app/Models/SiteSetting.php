<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

#[Fillable([
    'site_name_ar', 'site_name_en', 'tagline_ar', 'tagline_en',
    'about_ar', 'about_en', 'vision_ar', 'vision_en', 'mission_ar', 'mission_en',
    'study_plan_intro_ar', 'study_plan_intro_en', 'regulations_ar', 'regulations_en',
    'address_ar', 'address_en', 'phone', 'email', 'whatsapp',
    'facebook', 'twitter', 'instagram', 'youtube', 'telegram',
    'logo', 'favicon',     'footer_text_ar', 'footer_text_en',
    'homepage_featured_courses_enabled', 'homepage_featured_courses_visible_from', 'homepage_featured_courses_visible_until',
    'website_primary_color', 'website_accent_color', 'website_background_color', 'website_color_palette',
    'website_nav_pages',
    'dashboard_institute_name_ar', 'dashboard_institute_name_en',
    'academic_year_ar', 'academic_year_en',
    'dashboard_welcome_ar', 'dashboard_welcome_en',
    'enable_forum', 'enable_live_lessons', 'enable_hifz',
    'enable_honor_board', 'enable_wallet',
    'dashboard_logo', 'dashboard_use_site_logo',
    'dashboard_primary_color', 'dashboard_sidebar_color', 'dashboard_accent_color',
    'dashboard_background_color', 'dashboard_color_palette', 'dashboard_style', 'dashboard_layout',
    'dashboard_sidebar_style', 'dashboard_show_pattern', 'dashboard_compact_mode',
    'admin_brand_name_ar', 'admin_brand_name_en', 'admin_logo', 'admin_use_site_logo',
    'admin_primary_color', 'admin_sidebar_color', 'admin_accent_color',
    'admin_background_color', 'admin_color_palette', 'admin_style', 'admin_layout', 'admin_navigation',
    'admin_sidebar_style', 'admin_sidebar_collapsible', 'admin_show_pattern',
    'admin_compact_mode',
])]
class SiteSetting extends Model
{
    protected function casts(): array
    {
        return [
            'enable_forum' => 'boolean',
            'enable_live_lessons' => 'boolean',
            'enable_hifz' => 'boolean',
            'enable_honor_board' => 'boolean',
            'enable_wallet' => 'boolean',
            'dashboard_use_site_logo' => 'boolean',
            'dashboard_show_pattern' => 'boolean',
            'dashboard_compact_mode' => 'boolean',
            'admin_use_site_logo' => 'boolean',
            'admin_sidebar_collapsible' => 'boolean',
            'admin_show_pattern' => 'boolean',
            'admin_compact_mode' => 'boolean',
            'homepage_featured_courses_enabled' => 'boolean',
            'homepage_featured_courses_visible_from' => 'datetime',
            'homepage_featured_courses_visible_until' => 'datetime',
            'website_nav_pages' => 'array',
        ];
    }

    public static function current(): self
    {
        if (! Schema::hasTable((new static)->getTable())) {
            return new static([
                'site_name_ar' => 'معهد علم شرعي',
                'admin_brand_name_ar' => 'معهد علم شرعي',
                'admin_primary_color' => '#059669',
                'admin_sidebar_color' => '#0f172a',
                'admin_layout' => 'wide',
                'admin_navigation' => 'sidebar',
                'admin_sidebar_collapsible' => true,
                'admin_compact_mode' => false,
            ]);
        }

        return static::query()->firstOrCreate([]);
    }
}
