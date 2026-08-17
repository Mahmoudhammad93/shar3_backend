<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->text('memorization_ar')->nullable()->after('description_en');
            $table->text('memorization_en')->nullable()->after('memorization_ar');
            $table->text('primary_text_ar')->nullable()->after('memorization_en');
            $table->text('primary_text_en')->nullable()->after('primary_text_ar');
            $table->text('supplementary_text_ar')->nullable()->after('primary_text_en');
            $table->text('supplementary_text_en')->nullable()->after('supplementary_text_ar');
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->text('study_plan_intro_ar')->nullable()->after('mission_en');
            $table->text('study_plan_intro_en')->nullable()->after('study_plan_intro_ar');
            $table->longText('regulations_ar')->nullable()->after('study_plan_intro_en');
            $table->longText('regulations_en')->nullable()->after('regulations_ar');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn([
                'memorization_ar',
                'memorization_en',
                'primary_text_ar',
                'primary_text_en',
                'supplementary_text_ar',
                'supplementary_text_en',
            ]);
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'study_plan_intro_ar',
                'study_plan_intro_en',
                'regulations_ar',
                'regulations_en',
            ]);
        });
    }
};
