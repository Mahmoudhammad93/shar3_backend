<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('media_type', 16)->nullable()->after('video_url');
            $table->string('video_provider', 16)->nullable()->after('media_type');
            $table->string('bunny_library_id')->nullable()->after('video_provider');
            $table->string('bunny_video_id')->nullable()->after('bunny_library_id');
            $table->string('bunny_status', 32)->nullable()->after('bunny_video_id');
            $table->string('google_drive_file_id')->nullable()->after('bunny_status');
            $table->string('google_drive_resource_key')->nullable()->after('google_drive_file_id');
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn([
                'media_type',
                'video_provider',
                'bunny_library_id',
                'bunny_video_id',
                'bunny_status',
                'google_drive_file_id',
                'google_drive_resource_key',
            ]);
        });
    }
};
