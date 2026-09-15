<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('dashboard_color_palette', 64)->default('custom')->after('dashboard_background_color');
            $table->string('admin_color_palette', 64)->default('custom')->after('admin_background_color');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['dashboard_color_palette', 'admin_color_palette']);
        });
    }
};
