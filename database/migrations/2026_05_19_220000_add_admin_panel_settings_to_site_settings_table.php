<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('admin_brand_name_ar')->nullable()->after('dashboard_compact_mode');
            $table->string('admin_brand_name_en')->nullable()->after('admin_brand_name_ar');
            $table->string('admin_logo')->nullable()->after('admin_brand_name_en');
            $table->boolean('admin_use_site_logo')->default(true)->after('admin_logo');
            $table->string('admin_primary_color')->default('#059669')->after('admin_use_site_logo');
            $table->string('admin_sidebar_color')->default('#0f172a')->after('admin_primary_color');
            $table->string('admin_accent_color')->default('#c9a227')->after('admin_sidebar_color');
            $table->string('admin_background_color')->default('#f8fafc')->after('admin_accent_color');
            $table->string('admin_style')->default('classic')->after('admin_background_color');
            $table->string('admin_layout')->default('wide')->after('admin_style');
            $table->string('admin_navigation')->default('sidebar')->after('admin_layout');
            $table->string('admin_sidebar_style')->default('dark')->after('admin_navigation');
            $table->boolean('admin_sidebar_collapsible')->default(true)->after('admin_sidebar_style');
            $table->boolean('admin_show_pattern')->default(false)->after('admin_sidebar_collapsible');
            $table->boolean('admin_compact_mode')->default(false)->after('admin_show_pattern');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'admin_brand_name_ar',
                'admin_brand_name_en',
                'admin_logo',
                'admin_use_site_logo',
                'admin_primary_color',
                'admin_sidebar_color',
                'admin_accent_color',
                'admin_background_color',
                'admin_style',
                'admin_layout',
                'admin_navigation',
                'admin_sidebar_style',
                'admin_sidebar_collapsible',
                'admin_show_pattern',
                'admin_compact_mode',
            ]);
        });
    }
};
