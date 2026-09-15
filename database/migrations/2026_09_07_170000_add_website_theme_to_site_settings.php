<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('website_primary_color', 32)->default('#002B5B')->after('homepage_featured_courses_visible_until');
            $table->string('website_accent_color', 32)->default('#C5A04D')->after('website_primary_color');
            $table->string('website_background_color', 32)->default('#f7f9fc')->after('website_accent_color');
            $table->string('website_color_palette', 64)->default('institute_navy_gold')->after('website_background_color');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'website_primary_color',
                'website_accent_color',
                'website_background_color',
                'website_color_palette',
            ]);
        });
    }
};
