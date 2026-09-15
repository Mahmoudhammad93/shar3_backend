<?php

namespace App\Support;

use App\Models\SiteSetting;
use Carbon\CarbonInterface;

class HomepageFeaturedCourses
{
    public static function shouldShow(?SiteSetting $settings = null, ?CarbonInterface $at = null): bool
    {
        $settings ??= SiteSetting::current();
        $at ??= now();

        if (! ($settings->homepage_featured_courses_enabled ?? false)) {
            return false;
        }

        if ($settings->homepage_featured_courses_visible_from
            && $at->lt($settings->homepage_featured_courses_visible_from)) {
            return false;
        }

        if ($settings->homepage_featured_courses_visible_until
            && $at->gt($settings->homepage_featured_courses_visible_until)) {
            return false;
        }

        return true;
    }
}
