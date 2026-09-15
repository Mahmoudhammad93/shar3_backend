<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('homepage_featured_courses_enabled')->default(false)->after('footer_text_en');
            $table->timestamp('homepage_featured_courses_visible_from')->nullable()->after('homepage_featured_courses_enabled');
            $table->timestamp('homepage_featured_courses_visible_until')->nullable()->after('homepage_featured_courses_visible_from');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'homepage_featured_courses_enabled',
                'homepage_featured_courses_visible_from',
                'homepage_featured_courses_visible_until',
            ]);
        });
    }
};
