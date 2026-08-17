<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('dashboard_institute_name_ar')->nullable()->after('footer_text_en');
            $table->string('dashboard_institute_name_en')->nullable()->after('dashboard_institute_name_ar');
            $table->string('academic_year_ar')->nullable()->after('dashboard_institute_name_en');
            $table->string('academic_year_en')->nullable()->after('academic_year_ar');
            $table->text('dashboard_welcome_ar')->nullable()->after('academic_year_en');
            $table->text('dashboard_welcome_en')->nullable()->after('dashboard_welcome_ar');
            $table->boolean('enable_forum')->default(true)->after('dashboard_welcome_en');
            $table->boolean('enable_live_lessons')->default(true)->after('enable_forum');
            $table->boolean('enable_hifz')->default(true)->after('enable_live_lessons');
            $table->boolean('enable_honor_board')->default(true)->after('enable_hifz');
            $table->boolean('enable_wallet')->default(true)->after('enable_honor_board');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'dashboard_institute_name_ar',
                'dashboard_institute_name_en',
                'academic_year_ar',
                'academic_year_en',
                'dashboard_welcome_ar',
                'dashboard_welcome_en',
                'enable_forum',
                'enable_live_lessons',
                'enable_hifz',
                'enable_honor_board',
                'enable_wallet',
            ]);
        });
    }
};
