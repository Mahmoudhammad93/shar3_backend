<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class SettingResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'site_name_ar' => $this->site_name_ar,
            'site_name_en' => $this->site_name_en,
            'tagline_ar' => $this->tagline_ar,
            'tagline_en' => $this->tagline_en,
            'about_ar' => $this->about_ar,
            'about_en' => $this->about_en,
            'vision_ar' => $this->vision_ar,
            'vision_en' => $this->vision_en,
            'mission_ar' => $this->mission_ar,
            'mission_en' => $this->mission_en,
            'study_plan_intro_ar' => $this->study_plan_intro_ar,
            'study_plan_intro_en' => $this->study_plan_intro_en,
            'regulations_ar' => $this->regulations_ar,
            'regulations_en' => $this->regulations_en,
            'address_ar' => $this->address_ar,
            'address_en' => $this->address_en,
            'phone' => $this->phone,
            'email' => $this->email,
            'whatsapp' => $this->whatsapp,
            'facebook' => $this->facebook,
            'twitter' => $this->twitter,
            'instagram' => $this->instagram,
            'youtube' => $this->youtube,
            'logo' => $this->mediaUrl($this->logo),
            'favicon' => $this->mediaUrl($this->favicon),
            'footer_text_ar' => $this->footer_text_ar,
            'footer_text_en' => $this->footer_text_en,
            'dashboard_institute_name_ar' => $this->dashboard_institute_name_ar,
            'dashboard_institute_name_en' => $this->dashboard_institute_name_en,
            'academic_year_ar' => $this->academic_year_ar,
            'academic_year_en' => $this->academic_year_en,
            'dashboard_welcome_ar' => $this->dashboard_welcome_ar,
            'dashboard_welcome_en' => $this->dashboard_welcome_en,
            'enable_forum' => $this->enable_forum ?? true,
            'enable_live_lessons' => $this->enable_live_lessons ?? true,
            'enable_hifz' => $this->enable_hifz ?? true,
            'enable_honor_board' => $this->enable_honor_board ?? true,
            'enable_wallet' => $this->enable_wallet ?? true,
            'dashboard_logo' => $this->mediaUrl($this->dashboard_logo),
            'dashboard_use_site_logo' => $this->dashboard_use_site_logo ?? true,
            'dashboard_primary_color' => $this->dashboard_primary_color ?? '#004d40',
            'dashboard_sidebar_color' => $this->dashboard_sidebar_color ?? '#0a3d34',
            'dashboard_accent_color' => $this->dashboard_accent_color ?? '#c9a227',
            'dashboard_background_color' => $this->dashboard_background_color ?? '#f4f7f6',
            'dashboard_style' => $this->dashboard_style ?? 'classic',
            'dashboard_layout' => $this->dashboard_layout ?? 'wide',
            'dashboard_sidebar_style' => $this->dashboard_sidebar_style ?? 'dark',
            'dashboard_show_pattern' => $this->dashboard_show_pattern ?? true,
            'dashboard_compact_mode' => $this->dashboard_compact_mode ?? false,
        ];
    }
}
