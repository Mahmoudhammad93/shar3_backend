<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('dashboard_logo')->nullable()->after('enable_wallet');
            $table->boolean('dashboard_use_site_logo')->default(true)->after('dashboard_logo');
            $table->string('dashboard_primary_color')->default('#004d40')->after('dashboard_use_site_logo');
            $table->string('dashboard_sidebar_color')->default('#0a3d34')->after('dashboard_primary_color');
            $table->string('dashboard_accent_color')->default('#c9a227')->after('dashboard_sidebar_color');
            $table->string('dashboard_background_color')->default('#f4f7f6')->after('dashboard_accent_color');
            $table->string('dashboard_style')->default('classic')->after('dashboard_background_color');
            $table->string('dashboard_layout')->default('wide')->after('dashboard_style');
            $table->string('dashboard_sidebar_style')->default('dark')->after('dashboard_layout');
            $table->boolean('dashboard_show_pattern')->default(true)->after('dashboard_sidebar_style');
            $table->boolean('dashboard_compact_mode')->default(false)->after('dashboard_show_pattern');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'dashboard_logo',
                'dashboard_use_site_logo',
                'dashboard_primary_color',
                'dashboard_sidebar_color',
                'dashboard_accent_color',
                'dashboard_background_color',
                'dashboard_style',
                'dashboard_layout',
                'dashboard_sidebar_style',
                'dashboard_show_pattern',
                'dashboard_compact_mode',
            ]);
        });
    }
};
